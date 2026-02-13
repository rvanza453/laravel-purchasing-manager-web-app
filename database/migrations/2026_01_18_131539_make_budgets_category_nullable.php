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
        Schema::table('budgets', function (Blueprint $table) {
            // Drop FK that depends on the unique index
            $table->dropForeign(['sub_department_id']);
            
            // Drop the old unique constraint
            $table->dropUnique(['sub_department_id', 'category', 'year']);
            
            // Restore FK
            $table->foreign('sub_department_id')->references('id')->on('sub_departments')->cascadeOnDelete();
            
            // Make category nullable since budgets can be by job_id OR category
            $table->string('category')->nullable()->change();
            
            // Add new unique constraints
            // For category-based budgets (station)
            $table->unique(['sub_department_id', 'category', 'year'], 'budgets_category_unique');
            
            // For job-based budgets (job_coa)
            $table->unique(['sub_department_id', 'job_id', 'year'], 'budgets_job_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Drop the new unique constraints
            $table->dropUnique('budgets_category_unique');
            $table->dropUnique('budgets_job_unique');
            
            // Make category not nullable again
            $table->string('category')->nullable(false)->change();
            
            // Restore the original unique constraint
            $table->unique(['sub_department_id', 'category', 'year']);
        });
    }
};
