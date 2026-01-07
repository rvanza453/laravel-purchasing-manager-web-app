<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Starting Manual Schema Repair...\n";

    // 1. Fix Users Table
    if (!Schema::hasColumn('users', 'site_id')) {
        echo "Adding site_id to users...\n";
        DB::statement('ALTER TABLE users ADD COLUMN site_id BIGINT NULL REFERENCES sites(id) ON DELETE SET NULL');
    }
    if (!Schema::hasColumn('users', 'department_id')) {
        echo "Adding department_id to users...\n";
        DB::statement('ALTER TABLE users ADD COLUMN department_id BIGINT NULL REFERENCES departments(id) ON DELETE SET NULL');
    }

    // 2. Create products
    if (!Schema::hasTable('products')) {
        echo "Creating products table...\n";
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('unit'); 
            $table->integer('min_stock')->default(0);
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    // 3. Create pr_approvals
    if (!Schema::hasTable('pr_approvals')) {
        echo "Creating pr_approvals table...\n";
        Schema::create('pr_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users');
            $table->integer('level');
            $table->string('role_name');
            $table->string('status')->default('Pending');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['approver_id', 'status']);
        });
    }

    // 4. Create warehouse_stocks
    if (!Schema::hasTable('warehouse_stocks')) {
        echo "Creating warehouse_stocks table...\n";
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'site_id']);
        });
    }

    // 5. Create stock_movements
    if (!Schema::hasTable('stock_movements')) {
        echo "Creating stock_movements table...\n";
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('site_id')->constrained('sites');
            $table->foreignId('user_id')->constrained('users');
            $table->string('type'); 
            $table->integer('quantity');
            $table->string('reference_number')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('date');
            $table->timestamps();
        });
    }

    echo "Schema Repair Completed Successfully!\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
