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
            $table->integer('quantity')->default(1)->after('amount');
            $table->decimal('price', 15, 2)->default(0)->after('quantity');
            $table->string('type')->default('New')->after('price'); // New, Modification, Replacement
            $table->boolean('code_budget_ditanam')->default(true)->after('type'); // Is Budgeted?
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capex_requests', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'price', 'type', 'code_budget_ditanam']);
        });
    }
};
