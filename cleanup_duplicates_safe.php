<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

function chunkedDeleteByIds(string $table, array $ids, int $chunk = 500): int
{
    $deleted = 0;
    foreach (array_chunk($ids, $chunk) as $part) {
        $deleted += DB::table($table)->whereIn('id', $part)->delete();
    }
    return $deleted;
}

echo "=== SAFE DUPLICATE CLEANUP ===\n\n";

echo "Rule for stock duplicate cleanup (strict):\n";
echo "- Same warehouse_id, product_id, type, DATE(date), quantity, price\n";
echo "- Same reference_number, remarks, job_id, sub_department_id\n";
echo "- Same created_at timestamp\n";
echo "- Keep smallest id per group\n\n";

$beforeStock = DB::table('stock_movements')->count();
$beforeBudgets = DB::table('budgets')->count();

$dupRows = DB::select(<<<'SQL'
SELECT sm.id, sm.type, sm.job_id, sm.sub_department_id, YEAR(sm.date) AS y
FROM stock_movements sm
JOIN (
    SELECT
        MIN(id) AS keep_id,
        warehouse_id,
        product_id,
        type,
        DATE(date) AS d,
        quantity,
        price,
        COALESCE(reference_number, '') AS ref,
        COALESCE(remarks, '') AS rem,
        COALESCE(job_id, 0) AS jid,
        COALESCE(sub_department_id, 0) AS sid,
        created_at,
        COUNT(*) AS c
    FROM stock_movements
    GROUP BY
        warehouse_id,
        product_id,
        type,
        DATE(date),
        quantity,
        price,
        COALESCE(reference_number, ''),
        COALESCE(remarks, ''),
        COALESCE(job_id, 0),
        COALESCE(sub_department_id, 0),
        created_at
    HAVING COUNT(*) > 1
) g ON sm.warehouse_id = g.warehouse_id
   AND sm.product_id = g.product_id
   AND sm.type = g.type
   AND DATE(sm.date) = g.d
   AND sm.quantity = g.quantity
   AND sm.price = g.price
   AND COALESCE(sm.reference_number, '') = g.ref
   AND COALESCE(sm.remarks, '') = g.rem
   AND COALESCE(sm.job_id, 0) = g.jid
   AND COALESCE(sm.sub_department_id, 0) = g.sid
   AND sm.created_at = g.created_at
   AND sm.id <> g.keep_id
SQL);

$dupIds = array_map(static fn($r) => (int) $r->id, $dupRows);

echo "Before cleanup:\n";
echo "- stock_movements: {$beforeStock}\n";
echo "- budgets: {$beforeBudgets}\n";
echo "- strict duplicate stock rows to delete: " . count($dupIds) . "\n\n";

$budgetMismatches = DB::select(<<<'SQL'
SELECT
    b.id,
    b.job_id,
    b.year,
    b.department_id AS wrong_department_id,
    j.department_id AS correct_department_id,
    b.category,
    b.amount,
    b.pta_amount,
    b.used_amount
FROM budgets b
JOIN jobs j ON j.id = b.job_id
WHERE b.job_id IS NOT NULL
  AND b.department_id IS NOT NULL
  AND b.department_id <> j.department_id
SQL);

echo "- budget dept/job mismatches: " . count($budgetMismatches) . "\n\n";

echo "Continue with APPLY? (yes/no): ";
$answer = strtolower(trim(fgets(STDIN)));
if ($answer !== 'yes') {
    echo "Canceled. No changes applied.\n";
    exit(0);
}

$ts = date('Ymd_His');
$backupStockTable = "stock_movements_cleanup_bkp_{$ts}";
$backupBudgetTable = "budgets_cleanup_bkp_{$ts}";

try {
    if (!empty($dupIds)) {
        DB::statement("CREATE TABLE {$backupStockTable} AS SELECT * FROM stock_movements WHERE id IN (" . implode(',', $dupIds) . ")");
    } else {
        DB::statement("CREATE TABLE {$backupStockTable} AS SELECT * FROM stock_movements WHERE 1=0");
    }

    if (!empty($budgetMismatches)) {
        $mismatchIds = implode(',', array_map(static fn($r) => (int) $r->id, $budgetMismatches));
        DB::statement("CREATE TABLE {$backupBudgetTable} AS SELECT * FROM budgets WHERE id IN ({$mismatchIds})");
    } else {
        DB::statement("CREATE TABLE {$backupBudgetTable} AS SELECT * FROM budgets WHERE 1=0");
    }

    if (empty($dupIds) && empty($budgetMismatches)) {
        echo "\nNo actionable rows found. Backup tables were still created:\n";
        echo "- {$backupStockTable}\n";
        echo "- {$backupBudgetTable}\n";
        exit(0);
    }

    DB::beginTransaction();

    $affectedJobYears = [];
    $affectedSubYears = [];
    foreach ($dupRows as $row) {
        if ($row->type !== 'OUT') {
            continue;
        }
        if (!is_null($row->job_id)) {
            $key = $row->job_id . ':' . $row->y;
            $affectedJobYears[$key] = [(int) $row->job_id, (int) $row->y];
        } elseif (!is_null($row->sub_department_id)) {
            $key = $row->sub_department_id . ':' . $row->y;
            $affectedSubYears[$key] = [(int) $row->sub_department_id, (int) $row->y];
        }
    }

    $deletedStock = chunkedDeleteByIds('stock_movements', $dupIds);

    $budgetMerged = 0;
    $budgetDeleted = 0;
    foreach ($budgetMismatches as $m) {
        $target = DB::table('budgets')
            ->where('job_id', $m->job_id)
            ->where('year', $m->year)
            ->where('department_id', $m->correct_department_id)
            ->orderBy('id')
            ->first();

        if ($target) {
            $newUsed = max((float) ($target->used_amount ?? 0), (float) ($m->used_amount ?? 0));
            DB::table('budgets')->where('id', $target->id)->update(['used_amount' => $newUsed]);
            $budgetMerged++;
        } else {
            DB::table('budgets')->insert([
                'sub_department_id' => null,
                'department_id' => $m->correct_department_id,
                'job_id' => $m->job_id,
                'category' => $m->category,
                'amount' => $m->amount ?? 0,
                'pta_amount' => $m->pta_amount ?? 0,
                'used_amount' => $m->used_amount ?? 0,
                'year' => $m->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $budgetMerged++;
        }

        DB::table('budgets')->where('id', $m->id)->delete();
        $budgetDeleted++;

        $jKey = $m->job_id . ':' . $m->year;
        $affectedJobYears[$jKey] = [(int) $m->job_id, (int) $m->year];
    }

    $recalcJob = 0;
    foreach ($affectedJobYears as [$jobId, $year]) {
        $sum = (float) (DB::table('stock_movements')
            ->where('type', 'OUT')
            ->where('job_id', $jobId)
            ->whereYear('date', $year)
            ->selectRaw('COALESCE(SUM(quantity * price), 0) AS total')
            ->value('total') ?? 0);

        DB::table('budgets')
            ->where('job_id', $jobId)
            ->where('year', $year)
            ->update(['used_amount' => $sum]);
        $recalcJob++;
    }

    $recalcSub = 0;
    $skippedSub = 0;
    foreach ($affectedSubYears as [$subId, $year]) {
        $rows = DB::table('budgets')
            ->where('sub_department_id', $subId)
            ->where('year', $year)
            ->orderBy('id')
            ->get();

        if ($rows->count() !== 1) {
            $skippedSub++;
            continue;
        }

        $sum = (float) (DB::table('stock_movements')
            ->where('type', 'OUT')
            ->where('sub_department_id', $subId)
            ->whereYear('date', $year)
            ->selectRaw('COALESCE(SUM(quantity * price), 0) AS total')
            ->value('total') ?? 0);

        DB::table('budgets')->where('id', $rows->first()->id)->update(['used_amount' => $sum]);
        $recalcSub++;
    }

    DB::commit();

    $afterStock = DB::table('stock_movements')->count();
    $afterBudgets = DB::table('budgets')->count();

    echo "\n=== APPLIED SUCCESSFULLY ===\n";
    echo "Backup tables:\n";
    echo "- {$backupStockTable}\n";
    echo "- {$backupBudgetTable}\n\n";
    echo "Stock movements:\n";
    echo "- before: {$beforeStock}\n";
    echo "- deleted: {$deletedStock}\n";
    echo "- after: {$afterStock}\n\n";
    echo "Budget fixes:\n";
    echo "- mismatch merged/created: {$budgetMerged}\n";
    echo "- mismatch deleted: {$budgetDeleted}\n";
    echo "- job-year recalculated: {$recalcJob}\n";
    echo "- subdept-year recalculated: {$recalcSub}\n";
    echo "- subdept-year skipped (ambiguous categories): {$skippedSub}\n";
    echo "- budgets after: {$afterBudgets}\n";

} catch (Throwable $e) {
    if (DB::transactionLevel() > 0) {
        DB::rollBack();
    }
    echo "\nERROR: " . $e->getMessage() . "\n";
    exit(1);
}
