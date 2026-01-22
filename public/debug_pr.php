<?php
// Debug script to check PR data
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\PurchaseRequest;

// Try to find the PR from the screenshot
$prNumber = '0014/KDE-SSM/I/2026';
$pr = PurchaseRequest::where('pr_number', $prNumber)->with(['items.job', 'subDepartment'])->first();

if (!$pr) {
    echo "PR $prNumber not found. Listing latest 5 PRs:\n";
    $latest = PurchaseRequest::latest()->take(5)->get();
    foreach ($latest as $p) {
        echo "- {$p->pr_number} (ID: {$p->id})\n";
    }
} else {
    echo "PR Found: {$pr->pr_number}\n";
    echo "Department: " . ($pr->department ? $pr->department->name : 'N/A') . "\n";
    echo "SubDepartment: " . ($pr->subDepartment ? $pr->subDepartment->name : 'N/A') . "\n";
    echo "Budget Type (from Dept): " . ($pr->department ? $pr->department->budget_type : 'N/A') . "\n";
    
    echo "\nItems:\n";
    foreach ($pr->items as $item) {
        echo "- Item: {$item->item_name}\n";
        echo "  Job ID: " . var_export($item->job_id, true) . "\n";
        if ($item->job) {
            echo "  Job Details: {$item->job->code} - {$item->job->name}\n";
        } else {
            echo "  Job Relation: NULL\n";
        }
    }
}
