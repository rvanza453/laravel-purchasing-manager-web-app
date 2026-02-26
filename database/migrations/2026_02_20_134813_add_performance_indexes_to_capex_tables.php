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
        Schema::table('capex_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('current_step');
            $table->index('created_at');
            $table->index('updated_at'); // Often used for sorting
        });

        Schema::table('capex_budgets', function (Blueprint $table) {
            $table->index('fiscal_year');
            $table->index('is_active');
            $table->index('remaining_amount');
            $table->index('budget_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capex_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['current_step']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('capex_budgets', function (Blueprint $table) {
            $table->dropIndex(['fiscal_year']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['remaining_amount']);
            $table->dropIndex(['budget_code']);
        });
    }
};
