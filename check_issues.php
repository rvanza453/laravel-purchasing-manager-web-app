<?php

// Diagnostic script to check PR and role issues
// Run with: php check_issues.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSTIC REPORT ===\n\n";

// Check Purchase Requests
echo "1. PURCHASE REQUESTS:\n";
$prs = \App\Models\PurchaseRequest::all();
echo "Total PRs: " . $prs->count() . "\n";
foreach ($prs as $pr) {
    echo "  - PR #{$pr->pr_number} | User ID: {$pr->user_id} | Status: {$pr->status}\n";
}
echo "\n";

// Check Users
echo "2. USERS:\n";
$users = \App\Models\User::all();
echo "Total Users: " . $users->count() . "\n";
foreach ($users as $user) {
    echo "  - ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
}
echo "\n";

// Check Roles
echo "3. ROLES:\n";
$roles = \Spatie\Permission\Models\Role::all();
echo "Total Roles: " . $roles->count() . "\n";
foreach ($roles as $role) {
    echo "  - {$role->name}\n";
}
echo "\n";

// Check User Roles
echo "4. USER ROLE ASSIGNMENTS:\n";
foreach ($users as $user) {
    $userRoles = $user->getRoleNames()->implode(', ');
    echo "  - {$user->name}: " . ($userRoles ?: 'No roles') . "\n";
}
echo "\n";

// Check if PRs have valid users
echo "5. PR USER VALIDATION:\n";
foreach ($prs as $pr) {
    $userExists = \App\Models\User::find($pr->user_id) ? 'EXISTS' : 'MISSING';
    echo "  - PR #{$pr->pr_number} -> User ID {$pr->user_id}: {$userExists}\n";
}
echo "\n";

echo "=== END REPORT ===\n";
