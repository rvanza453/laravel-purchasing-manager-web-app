<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CLEANING UP INCOMPATIBLE BUDGETS ===\n\n";

$departments = \App\Models\Department::with('subDepartments')->get();

$totalDeleted = 0;

foreach ($departments as $dept) {
    echo "Department: {$dept->name} (Type: {$dept->budget_type->value})\n";
    
    foreach ($dept->subDepartments as $subDept) {
        if ($dept->budget_type === \App\Enums\BudgetingType::JOB_COA) {
            // Delete category-based budgets
            $deleted = \App\Models\Budget::where('sub_department_id', $subDept->id)
                ->whereNotNull('category')
                ->delete();
            
            if ($deleted > 0) {
                echo "  - {$subDept->name}: Deleted {$deleted} category-based budgets\n";
                $totalDeleted += $deleted;
            }
        } else {
            // Delete job-based budgets
            $deleted = \App\Models\Budget::where('sub_department_id', $subDept->id)
                ->whereNotNull('job_id')
                ->delete();
            
            if ($deleted > 0) {
                echo "  - {$subDept->name}: Deleted {$deleted} job-based budgets\n";
                $totalDeleted += $deleted;
            }
        }
    }
}

echo "\n=== CLEANUP COMPLETE ===\n";
echo "Total incompatible budgets deleted: {$totalDeleted}\n";
