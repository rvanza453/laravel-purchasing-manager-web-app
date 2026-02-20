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
        Schema::create('capex_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('capex_asset_id')->constrained('capex_assets')->cascadeOnDelete();
            $table->string('budget_code'); // e.g. 061-xxx
            $table->decimal('amount', 15, 2);
            $table->decimal('remaining_amount', 15, 2);
            $table->year('fiscal_year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capex_budgets');
    }
};
