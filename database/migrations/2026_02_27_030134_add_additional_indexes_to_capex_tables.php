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
        // capex_requests: add FK indexes for filtering by budget, department, user
        Schema::table('capex_requests', function (Blueprint $table) {
            $table->index('capex_budget_id');
            $table->index('department_id');
            $table->index('user_id');
        });

        // capex_budgets: add FK indexes for lookups by department and asset
        Schema::table('capex_budgets', function (Blueprint $table) {
            $table->index('capex_asset_id');
            $table->index('department_id');
        });

        // capex_approvals: index status to quickly filter pending/approved/rejected steps
        Schema::table('capex_approvals', function (Blueprint $table) {
            $table->index('status');
            $table->index('approver_id');
        });

        // capex_column_configs: index department_id and column_index (now dept-aware)
        Schema::table('capex_column_configs', function (Blueprint $table) {
            if (Schema::hasColumn('capex_column_configs', 'department_id')) {
                $table->index(['department_id', 'column_index']);
            }
        });

        // capex_assets: index is_active for filtered lookups
        Schema::table('capex_assets', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capex_requests', function (Blueprint $table) {
            $table->dropIndex(['capex_budget_id']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('capex_budgets', function (Blueprint $table) {
            $table->dropIndex(['capex_asset_id']);
            $table->dropIndex(['department_id']);
        });

        Schema::table('capex_approvals', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['approver_id']);
        });

        Schema::table('capex_column_configs', function (Blueprint $table) {
            if (Schema::hasColumn('capex_column_configs', 'department_id')) {
                $table->dropIndex(['department_id', 'column_index']);
            }
        });

        Schema::table('capex_assets', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }
};
