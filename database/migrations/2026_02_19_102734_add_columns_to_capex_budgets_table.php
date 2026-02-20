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
        Schema::table('capex_budgets', function (Blueprint $table) {
            $table->integer('original_quantity')->default(1)->after('amount');
            $table->integer('remaining_quantity')->default(1)->after('original_quantity');
            $table->boolean('is_budgeted')->default(true)->after('remaining_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capex_budgets', function (Blueprint $table) {
            $table->dropColumn(['original_quantity', 'remaining_quantity', 'is_budgeted']);
        });
    }
};
