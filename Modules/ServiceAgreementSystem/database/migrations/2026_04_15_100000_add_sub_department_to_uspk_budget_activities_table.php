<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('uspk_budget_activities', 'sub_department_id')) {
            Schema::table('uspk_budget_activities', function (Blueprint $table) {
                $table->foreignId('sub_department_id')
                    ->nullable()
                    ->after('block_id')
                    ->constrained('sub_departments')
                    ->onDelete('cascade');
            });
        }

        DB::statement('UPDATE uspk_budget_activities uba
            INNER JOIN blocks b ON b.id = uba.block_id
            SET uba.sub_department_id = b.sub_department_id
            WHERE uba.sub_department_id IS NULL');

        // Konsolidasi data lama per block menjadi per afdeling + job + tahun.
        // Ini mencegah bentrok unique index baru saat 1 afdeling punya beberapa block pada job yang sama.
        DB::statement('CREATE TEMPORARY TABLE tmp_uspk_budget_rollup AS
            SELECT
                MIN(id) AS keep_id,
                sub_department_id,
                job_id,
                year,
                SUM(budget_amount) AS total_budget_amount,
                SUM(used_amount) AS total_used_amount,
                MAX(is_active) AS merged_is_active,
                GROUP_CONCAT(DISTINCT description SEPARATOR " | ") AS merged_description
            FROM uspk_budget_activities
            WHERE sub_department_id IS NOT NULL
            GROUP BY sub_department_id, job_id, year');

        DB::statement('UPDATE uspk_budget_activities uba
            INNER JOIN tmp_uspk_budget_rollup t ON t.keep_id = uba.id
            SET
                uba.budget_amount = t.total_budget_amount,
                uba.used_amount = t.total_used_amount,
                uba.is_active = t.merged_is_active,
                uba.description = t.merged_description');

        DB::statement('DELETE uba FROM uspk_budget_activities uba
            INNER JOIN tmp_uspk_budget_rollup t
                ON t.sub_department_id = uba.sub_department_id
                AND t.job_id = uba.job_id
                AND t.year = uba.year
            WHERE uba.id <> t.keep_id');

        DB::statement('DROP TEMPORARY TABLE IF EXISTS tmp_uspk_budget_rollup');

        Schema::table('uspk_budget_activities', function (Blueprint $table) {
            $table->index('block_id', 'uspk_budget_activities_block_id_index');
        });

        Schema::table('uspk_budget_activities', function (Blueprint $table) {
            $table->dropUnique('uspk_budget_block_job_year_unique');
            $table->unique(['sub_department_id', 'job_id', 'year'], 'uspk_budget_subdept_job_year_unique');
        });
    }

    public function down(): void
    {
        Schema::table('uspk_budget_activities', function (Blueprint $table) {
            $table->dropUnique('uspk_budget_subdept_job_year_unique');
            $table->dropIndex('uspk_budget_activities_block_id_index');
            $table->unique(['block_id', 'job_id', 'year'], 'uspk_budget_block_job_year_unique');
            $table->dropConstrainedForeignId('sub_department_id');
        });
    }
};
