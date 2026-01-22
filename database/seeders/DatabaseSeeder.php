<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Site;
use App\Models\Department;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\ApproverConfig;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Sites
        $sitePabrik = Site::create(['name' => 'Pabrik Sentul', 'code' => 'S01', 'location' => 'Bogor']);
        $siteHO = Site::create(['name' => 'Head Office', 'code' => 'HO', 'location' => 'Jakarta']);

        // 2. Create Departments
        $deptIT = Department::create(['site_id' => $siteHO->id, 'name' => 'IT Department', 'coa' => 'IT']);
        $deptHR = Department::create(['site_id' => $siteHO->id, 'name' => 'Human Resources', 'coa' => 'HR']);
        $deptProd = Department::create(['site_id' => $sitePabrik->id, 'name' => 'Production', 'coa' => 'PROD']);

        // 3. Roles & Permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleStaff = Role::create(['name' => 'Staff']);
        $roleApprover = Role::create(['name' => 'Approver']);
        $roleWarehouse = Role::create(['name' => 'Warehouse']);

        // 4. Create Users
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'site_id' => $siteHO->id,
            'department_id' => $deptIT->id,
            'position' => 'System Admin'
        ]);
        $admin->assignRole('Admin');

        $userStaff = User::create([
            'name' => 'Staff IT',
            'email' => 'staff@example.com',
            'password' => Hash::make('123456'),
            'site_id' => $siteHO->id,
            'department_id' => $deptIT->id,
            'position' => 'IT Support'
        ]);
        $userStaff->assignRole('Staff');

        $managerIT = User::create([
            'name' => 'Manager IT',
            'email' => 'manager.it@example.com',
            'password' => Hash::make('123456'),
            'site_id' => $siteHO->id,
            'department_id' => $deptIT->id,
            'position' => 'IT Manager'
        ]);
        $managerIT->assignRole('Approver');

        $gmIT = User::create([
            'name' => 'General Manager',
            'email' => 'gm@example.com',
            'password' => Hash::make('123456'),
            'site_id' => $siteHO->id,
            'department_id' => $deptIT->id,
            'position' => 'GM'
        ]);
        $gmIT->assignRole('Approver');

        // 5. Approver Config for IT Dept
        ApproverConfig::create([
            'department_id' => $deptIT->id,
            'level' => 1,
            'role_name' => 'Manager IT',
            'user_id' => $managerIT->id
        ]);
        ApproverConfig::create([
            'department_id' => $deptIT->id,
            'level' => 2,
            'role_name' => 'General Manager',
            'user_id' => $gmIT->id
        ]);

        // 6. Master Products
        Product::create(['name' => 'Laptop Dell Latitude', 'code' => 'LPT-001', 'unit' => 'Unit', 'category' => 'Electronics']);
        Product::create(['name' => 'Mouse Logitech', 'code' => 'ACC-001', 'unit' => 'Pcs', 'category' => 'Electronics']);
        Product::create(['name' => 'Kertas A4', 'code' => 'ATK-001', 'unit' => 'Rim', 'category' => 'ATK']);
        Product::create(['name' => 'Bantalan Stempel', 'code' => 'ATK-002', 'unit' => 'Pcs', 'category' => 'ATK']);
        Product::create(['name' => 'Oli Mesin', 'code' => 'SP-001', 'unit' => 'Drum', 'category' => 'Sparepart']);
    }
}
