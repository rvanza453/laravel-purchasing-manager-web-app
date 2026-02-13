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
        Schema::table('job_coas', function (Blueprint $table) {
            // Drop Foreign Key first (if exists - usually tablename_column_foreign)
            // Or try catch if unsure, but standard naming is:
            $table->dropForeign(['sub_department_id']);
            $table->dropColumn('sub_department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_coas', function (Blueprint $table) {
            $table->foreignId('sub_department_id')->nullable()->constrained('sub_departments')->onDelete('cascade');
        });
    }
};
