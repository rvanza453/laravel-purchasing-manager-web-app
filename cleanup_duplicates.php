<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DUPLICATE CLEANUP PROCESS ===\n\n";

// STEP 1: Backup & Analysis
echo "STEP 1: Backup & Analysis\n";
echo "==========================\n";

$before = [
    'stock_movements' => DB::table('stock_movements')->count(),
    'budgets' => DB::table('budgets')->count(),
];
echo "Before cleanup:\n";
echo "  - stock_movements: {$before['stock_movements']}\n";
echo "  - budgets: {$before['budgets']}\n\n";

// Hitung duplikat yang akan dihapus
$dupMovementCount = DB::select("
    SELECT warehouse_id, product_id, type, DATE(date) as date, quantity, price, COUNT(*) as cnt
    FROM stock_movements
    GROUP BY warehouse_id, product_id, type, DATE(date), quantity, price
    HAVING cnt > 1
");

$totalDupGroups = count($dupMovementCount);
$totalRowsToDelete = array_sum(array_map(fn($g) => $g->cnt - 1, $dupMovementCount));

echo "Duplicate stock_movements to clean:\n";
echo "  - Duplicate groups: {$totalDupGroups}\n";
echo "  - Rows to delete: {$totalRowsToDelete}\n\n";

// Hitung budget anomali
$budgetAnomalies = DB::select("
    SELECT 
        b.id, 
        b.job_id, 
        b.year, 
        b.department_id as incorrect_dept_id,
        j.department_id as correct_dept_id,
        b.used_amount
    FROM budgets b
    INNER JOIN jobs j ON b.job_id = j.id
    WHERE b.job_id IS NOT NULL 
        AND b.department_id != j.department_id
        AND b.department_id IS NOT NULL
");

echo "Budget anomalies (dept_id mismatch with job):\n";
echo "  - Count: " . count($budgetAnomalies) . "\n";
foreach ($budgetAnomalies as $anom) {
    echo "    ID {$anom->id}: Job {$anom->job_id} should be Dept {$anom->correct_dept_id}, not {$anom->incorrect_dept_id}\n";
}

// Check for job=1400 logicdup
$job1400dups = DB::select("
    SELECT id, department_id, job_id, used_amount, created_at
    FROM budgets
    WHERE job_id = 1400 AND year = 2026
    ORDER BY department_id, created_at
");
echo "\nJob 1400 (year 2026) budget rows:\n";
foreach ($job1400dups as $b) {
    echo "  ID {$b->id}: Dept={$b->department_id}, Used={$b->used_amount}, Created={$b->created_at}\n";
}

echo "\n--- STARTING CLEANUP ---\n\n";

try {
    DB::beginTransaction();

    // STEP 2: Clean stock_movements
    echo "STEP 2: Clean stock_movements duplicates\n";
    echo "========================================\n";

    // Get all duplicate groups
    $dupGroups = DB::select("
        SELECT warehouse_id, product_id, type, DATE(date) as date, quantity, price, COUNT(*) as cnt, GROUP_CONCAT(id) as ids
        FROM stock_movements
        GROUP BY warehouse_id, product_id, type, DATE(date), quantity, price
        HAVING cnt > 1
    ");

    $deletedCount = 0;
    foreach ($dupGroups as $group) {
        $ids = explode(',', $group->ids);
        // Keep the smallest ID (first record), delete others
        $keepId = min($ids);
        $deleteIds = array_filter($ids, fn($id) => $id !== $keepId);
        
        $count = DB::table('stock_movements')
            ->whereIn('id', $deleteIds)
            ->delete();
        
        $deletedCount += $count;
        echo "  Group: W{$group->warehouse_id} P{$group->product_id} ({$group->type}) Qty={$group->quantity} @ {$group->price}\n";
        echo "    → Kept ID {$keepId}, deleted " . count($deleteIds) . " duplicates\n";
    }

    echo "\nTotal stock_movement rows deleted: $deletedCount\n\n";

    // STEP 3: Clean budget anomalies
    echo "STEP 3: Clean budget anomalies (merge mismatched department)\n";
    echo "===========================================================\n";

    foreach ($budgetAnomalies as $anom) {
        // Find the correct budget (same job_id, year, correct department_id)
        $correctBudget = DB::table('budgets')
            ->where('job_id', $anom->job_id)
            ->where('year', $anom->year)
            ->where('department_id', $anom->correct_dept_id)
            ->first();

        if ($correctBudget) {
            // Merge used_amount to correct budget
            $newUsedAmount = ($correctBudget->used_amount ?? 0) + ($anom->used_amount ?? 0);
            DB::table('budgets')
                ->where('id', $correctBudget->id)
                ->update(['used_amount' => $newUsedAmount]);
            
            echo "  ID {$anom->id}: Merged {$anom->used_amount} to correct budget ID {$correctBudget->id}\n";
            echo "    New total: {$newUsedAmount}\n";
        } else {
            // If correct budget doesn't exist, create it with merged amount
            DB::table('budgets')->insert([
                'job_id' => $anom->job_id,
                'year' => $anom->year,
                'department_id' => $anom->correct_dept_id,
                'amount' => 0,
                'pta_amount' => 0,
                'used_amount' => $anom->used_amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "  ID {$anom->id}: Created correct budget for Dept {$anom->correct_dept_id} with amount {$anom->used_amount}\n";
        }

        // Delete anomalous budget
        DB::table('budgets')->where('id', $anom->id)->delete();
        echo "    Deleted anomalous budget ID {$anom->id}\n\n";
    }

    // STEP 4: Recalculate used_amount for all budgets
    echo "STEP 4: Recalculate used_amount from clean stock_movements\n";
    echo "=========================================================\n";

    // Get all budgets with job_id or sub_department_id
    $allBudgets = DB::table('budgets')
        ->where(function($q) {
            $q->whereNotNull('job_id')->orWhereNotNull('sub_department_id');
        })
        ->get();

    $updatedBudgetCount = 0;
    foreach ($allBudgets as $budget) {
        $calculatedUsed = 0;

        if ($budget->job_id) {
            // Sum from stock_movements OUT where job matches
            $calculatedUsed = DB::table('stock_movements')
                ->where('job_id', $budget->job_id)
                ->where('type', 'OUT')
                ->selectRaw('SUM(quantity * price) as total')
                ->value('total') ?? 0;
        } elseif ($budget->sub_department_id) {
            // Sum from stock_movements OUT where sub_department matches
            $calculatedUsed = DB::table('stock_movements')
                ->where('sub_department_id', $budget->sub_department_id)
                ->where('type', 'OUT')
                ->selectRaw('SUM(quantity * price) as total')
                ->value('total') ?? 0;
        }

        $oldUsed = $budget->used_amount ?? 0;
        if (abs($calculatedUsed - $oldUsed) > 0.01) { // Allow small rounding diff
            DB::table('budgets')
                ->where('id', $budget->id)
                ->update(['used_amount' => $calculatedUsed]);
            
            $diff = $calculatedUsed - $oldUsed;
            echo "  Budget ID {$budget->id}: Recalculated from {$oldUsed} to {$calculatedUsed} (Δ {$diff})\n";
            $updatedBudgetCount++;
        }
    }

    echo "\nBudgets recalculated: $updatedBudgetCount\n\n";

    // Final stats
    $after = [
        'stock_movements' => DB::table('stock_movements')->count(),
        'budgets' => DB::table('budgets')->count(),
    ];

    echo "=== CLEANUP COMPLETE ===\n";
    echo "After cleanup:\n";
    echo "  - stock_movements: {$before['stock_movements']} → {$after['stock_movements']} (-" . ($before['stock_movements'] - $after['stock_movements']) . ")\n";
    echo "  - budgets: {$before['budgets']} → {$after['budgets']} (-" . ($before['budgets'] - $after['budgets']) . ")\n\n";

    // Ask for confirmation
    echo "Review complete. Do you want to COMMIT these changes? (yes/no): ";
    $input = trim(fgets(STDIN));

    if (strtolower($input) === 'yes') {
        DB::commit();
        echo "\n✓ Changes COMMITTED to database\n";
        
        // Log the action
        \Modules\PrSystem\Helpers\ActivityLogger::log('cleanup', 
            "Cleaned duplicate stock movements (+$deletedCount deleted) and balanced budgets");
    } else {
        DB::rollBack();
        echo "\n✗ Changes ROLLED BACK - no changes applied\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "All changes rolled back.\n";
    exit(1);
}

echo "\nDone.\n";
?>
