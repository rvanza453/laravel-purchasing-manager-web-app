<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fix Users Table (Add site_id, department_id)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'site_id')) {
                $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            }
        });

        // 2. Create pr_approvals if missing
        if (!Schema::hasTable('pr_approvals')) {
            Schema::create('pr_approvals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
                $table->foreignId('approver_id')->constrained('users');
                $table->integer('level');
                $table->string('role_name');
                $table->string('status')->default('Pending'); // Pending, Approved, Rejected
                $table->timestamp('approved_at')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->index(['approver_id', 'status']); // For "My Tasks" query
            });
        }

        // 3. Create products if missing
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('unit'); // Pcs, Unit, Rim
                $table->integer('min_stock')->default(0);
                $table->string('category')->nullable(); // Electronics, ATK, etc.
                $table->timestamps();
            });
        }

        // 4. Create warehouse_stocks if missing
        if (!Schema::hasTable('warehouse_stocks')) {
            Schema::create('warehouse_stocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->integer('quantity')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'site_id']);
            });
        }

        // 5. Create stock_movements if missing
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products');
                $table->foreignId('site_id')->constrained('sites');
                $table->foreignId('user_id')->constrained('users');
                $table->string('type'); // IN, OUT, ADJUST
                $table->integer('quantity');
                $table->string('reference_number')->nullable(); // PO Number or PR Number
                $table->text('remarks')->nullable();
                $table->timestamp('date');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // No reverse needed for emergency fix
    }
};
