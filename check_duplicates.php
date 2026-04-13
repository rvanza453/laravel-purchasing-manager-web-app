<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STOCK OUT DUPLICATES (Sample) ===\n";
$dupOut = DB::select("
    SELECT warehouse_id, product_id, type, DATE(date) as date, quantity, price, COUNT(*) as c, GROUP_CONCAT(id) as ids, GROUP_CONCAT(created_at) as created_ats
    FROM stock_movements
    WHERE type = 'OUT'
    GROUP BY warehouse_id, product_id, type, DATE(date), quantity, price
    HAVING c > 1
    LIMIT 8
");

foreach ($dupOut as $dup) {
    echo sprintf("Warehouse %d, Product %d, Qty %.4f @ %.2f = %d rows (IDs: %s)\n",
        $dup->warehouse_id, $dup->product_id, $dup->quantity, $dup->price, $dup->c, $dup->ids);
    echo "  Created at: " . $dup->created_ats . "\n";
}

echo "\n=== STOCK IN DUPLICATES ===\n";
$dupIn = DB::select("
    SELECT warehouse_id, product_id, type, DATE(date) as date, quantity, price, COUNT(*) as c, GROUP_CONCAT(id) as ids, GROUP_CONCAT(created_at) as created_ats
    FROM stock_movements
    WHERE type = 'IN'
    GROUP BY warehouse_id, product_id, type, DATE(date), quantity, price
    HAVING c > 1
");

if (empty($dupIn)) {
    echo "No IN duplicates found!\n";
} else {
    foreach ($dupIn as $dup) {
        echo sprintf("Warehouse %d, Product %d, Qty %.4f @ %.2f = %d rows (IDs: %s)\n",
            $dup->warehouse_id, $dup->product_id, $dup->quantity, $dup->price, $dup->c, $dup->ids);
        echo "  Created at: " . $dup->created_ats . "\n";
    }
}

echo "\n=== BUDGET DUPLICATE ANALYSIS ===\n";
// Check if unique constraint is actually enforced
$budgetDups = DB::select("
    SELECT 
        job_id, 
        department_id, 
        sub_department_id,
        category,
        year,
        COUNT(*) as cnt,
        GROUP_CONCAT(id ORDER BY id) as ids,
        GROUP_CONCAT(created_at ORDER BY created_at) as created_ats,
        GROUP_CONCAT(used_amount) as used_amounts
    FROM budgets
    WHERE job_id IS NOT NULL OR sub_department_id IS NOT NULL
    GROUP BY job_id, year, department_id, sub_department_id, category
    HAVING COUNT(*) > 1
    LIMIT 20
");

if (empty($budgetDups)) {
    echo "No budget duplicates by unique key! (Constraint working) ✓\n";
} else {
    echo "Budget duplicates found:\n";
    foreach ($budgetDups as $dup) {
        $jobId = $dup->job_id ?? 'NULL';
        $deptId = $dup->department_id ?? 'NULL';
        $subId = $dup->sub_department_id ?? 'NULL';
        $cat = $dup->category ?? 'NULL';
        echo sprintf("Job=%s Dept=%s SubDept=%s Category=%s Year=%d: %d rows (IDs: %s)\n",
            $jobId, $deptId, $subId, $cat,
            $dup->year, $dup->cnt, $dup->ids);
        echo "  Created: " . $dup->created_ats . "\n";
        echo "  Used amounts: " . $dup->used_amounts . "\n";
    }
}

echo "\n=== BUDGET ANOMALIES (Job 1400) ===\n";
$job1400 = DB::table('budgets')
    ->where('job_id', 1400)
    ->where('year', 2026)
    ->get(['id', 'job_id', 'department_id', 'sub_department_id', 'category', 'used_amount', 'created_at', 'updated_at']);

foreach ($job1400 as $b) {
    echo sprintf("ID %d: Dept=%s SubDept=%s Cat=%s UsedAmt=%.2f (created %s, updated %s)\n",
        $b->id, $b->department_id, $b->sub_department_id, $b->category, $b->used_amount, 
        $b->created_at, $b->updated_at);
}
?>
