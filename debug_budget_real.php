<?php

use App\Models\Budget;
use App\Models\SubDepartment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Budgets for 'Consumable' in 2026...\n";
echo "------------------------------------------------\n";

// 1. List ALL Categories in 2026
echo "Listing ALL Budget Categories found for Year 2026:\n";
$categories = Budget::where('year', 2026)->distinct()->pluck('category');

if ($categories->isEmpty()) {
    echo "NO BUDGETS FOUND FOR 2026 AT ALL.\n";
    
    // Check 2025 just in case
    echo "Checking 2025...\n";
    $cats25 = Budget::where('year', 2025)->distinct()->pluck('category');
    foreach ($cats25 as $c) {
        echo "- {$c} (Year 2025)\n";
    }
} else {
    foreach ($categories as $c) {
        echo "- '{$c}' (Length: " . strlen($c) . ")\n";
    }
}

// 2. Dump first 5 budget rows regardless of filter to ensure table isn't empty
echo "\nSample 5 rows from Budgets table:\n";
$all = Budget::take(5)->get();
foreach ($all as $b) {
    echo "ID: {$b->id}, SubDept: {$b->sub_department_id}, Cat: '{$b->category}', Year: {$b->year}, Job: " . var_export($b->job_id, true) . "\n";
}

