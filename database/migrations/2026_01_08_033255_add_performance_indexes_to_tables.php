<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pr_items', function (Blueprint $table) {
            // Composite index for JOIN queries (PR -> Items)
            $table->index(['purchase_request_id', 'product_id'], 'idx_pr_items_pr_product');
        });

        Schema::table('pr_approvals', function (Blueprint $table) {
            // Composite index for sequential approval workflow
            $table->index(['purchase_request_id', 'level'], 'idx_pr_approvals_pr_level');
            // Index for finding pending approvals by status and date
            $table->index(['status', 'created_at'], 'idx_pr_approvals_status_date');
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            // Composite index for filtering by user and status
            $table->index(['user_id', 'status'], 'idx_pr_user_status');
            // Composite index for department reports
            $table->index(['department_id', 'request_date'], 'idx_pr_dept_date');
        });

        Schema::table('products', function (Blueprint $table) {
            // Index for category filtering
            $table->index('category', 'idx_products_category');
        });

        Schema::table('departments', function (Blueprint $table) {
            // Composite index for site-based queries
            $table->index(['site_id', 'use_global_approval'], 'idx_dept_site_global');
        });

        Schema::table('approver_configs', function (Blueprint $table) {
            // Composite index for department approval chain
            $table->index(['department_id', 'level'], 'idx_approver_dept_level');
        });

        Schema::table('global_approver_configs', function (Blueprint $table) {
            // Index for level ordering
            $table->index('level', 'idx_global_approver_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pr_items', function (Blueprint $table) {
            $table->dropIndex('idx_pr_items_pr_product');
        });

        Schema::table('pr_approvals', function (Blueprint $table) {
            $table->dropIndex('idx_pr_approvals_pr_level');
            $table->dropIndex('idx_pr_approvals_status_date');
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropIndex('idx_pr_user_status');
            $table->dropIndex('idx_pr_dept_date');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_category');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex('idx_dept_site_global');
        });

        Schema::table('approver_configs', function (Blueprint $table) {
            $table->dropIndex('idx_approver_dept_level');
        });

        Schema::table('global_approver_configs', function (Blueprint $table) {
            $table->dropIndex('idx_global_approver_level');
        });
    }
};
