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
        Schema::table('capex_column_configs', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            
            // Drop existing unique constraint on column_index if it exists
            $table->dropUnique(['column_index']);
            
            // Add new composite unique constraint
            $table->unique(['department_id', 'column_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capex_column_configs', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropUnique(['department_id', 'column_index']);
            $table->dropColumn('department_id');
            
            // Restore unique constraint on column_index (might fail if duplicates exist)
            $table->unique('column_index');
        });
    }
};
