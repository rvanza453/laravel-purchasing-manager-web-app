<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE budgets DROP CONSTRAINT IF EXISTS budgets_sub_department_id_category_year_unique');
        } catch (\Exception $e) {
        }

        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('sub_department_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('sub_department_id')->nullable()->change();
            
            // Create new unique constraint
            $table->unique(['department_id', 'job_id', 'year'], 'budgets_dept_job_year_unique');
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropUnique('budgets_dept_job_year_unique');
            $table->dropColumn('department_id');
            $table->unique(['sub_department_id', 'category', 'year']);
        });
    }
};
