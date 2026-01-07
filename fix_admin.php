<?php

// Fix admin access issues
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING ADMIN ACCESS ===\n\n";

// Find admin user
$admin = \App\Models\User::where('email', 'admin@example.com')->first();

if (!$admin) {
    echo "ERROR: admin@example.com not found!\n";
    exit(1);
}

echo "Found user: {$admin->name} (ID: {$admin->id})\n";

// Check if admin role exists
$adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();

if (!$adminRole) {
    echo "Creating 'admin' role...\n";
    $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
}

// Assign admin role
if (!$admin->hasRole('admin')) {
    echo "Assigning admin role...\n";
    $admin->assignRole('admin');
    echo "✓ Admin role assigned!\n";
} else {
    echo "✓ User already has admin role\n";
}

// Show current roles
echo "\nCurrent roles for {$admin->name}: " . $admin->getRoleNames()->implode(', ') . "\n";

echo "\n=== DONE ===\n";
