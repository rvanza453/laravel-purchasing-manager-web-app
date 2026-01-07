<?php

use App\Models\User;
use App\Models\Site;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Checking requirements...\n";
    
    // 1. Ensure Migrations match Model
    if (!Schema::hasColumn('users', 'site_id')) {
        echo "WARNING: 'site_id' column missing on users table. Running migrations...\n";
        Artisan::call('migrate');
        echo Artisan::output();
    }

    // 2. Ensure Sites & Depts
    $site = Site::firstOrCreate(
        ['code' => 'HO'],
        ['name' => 'Head Office', 'location' => 'Jakarta']
    );
    echo "Site OK: " . $site->name . "\n";

    $dept = Department::firstOrCreate(
        ['code' => 'IT', 'site_id' => $site->id],
        ['name' => 'IT Department']
    );
    echo "Dept OK: " . $dept->name . "\n";

    // 3. Ensure Roles
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Role::firstOrCreate(['name' => 'Admin']);

    // 4. Create User
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
    
    echo "SUCCESS! User created:\n";
    echo "Email: " . $user->email . "\n";
    echo "Password: 123456\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
