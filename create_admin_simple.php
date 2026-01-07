<?php

use App\Models\User;
use App\Models\Site;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. Ensure Sites & Depts (Idempotent)
    $site = Site::firstOrCreate(
        ['code' => 'HO'],
        ['name' => 'Head Office', 'location' => 'Jakarta']
    );
    
    $dept = Department::firstOrCreate(
        ['code' => 'IT', 'site_id' => $site->id],
        ['name' => 'IT Department']
    );

    // 2. Ensure Roles
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Role::firstOrCreate(['name' => 'Admin']);

    // 3. Create User
    $user = User::updateOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Super Admin',
            'password' => Hash::make('123456'),
            'site_id' => $site->id,
            'department_id' => $dept->id,
            'position' => 'System Admin'
        ]
    );
    $user->assignRole('Admin');
    
    echo "SUCCESS: Admin created (admin@example.com / 123456)\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
