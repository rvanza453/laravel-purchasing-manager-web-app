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
        // 1. Update jobs table
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('code')->after('id')->nullable(); 
            // We need to drop foreign key first. We need to know the name. 
            // Usually table_column_foreign. jobs_job_coa_id_foreign
            $table->dropForeign(['job_coa_id']);
            $table->dropColumn('job_coa_id');
        });

        // 2. Update budgets table
        Schema::table('budgets', function (Blueprint $table) {
            // Add job_id FK
            // Note: job_coa_id drop removed as it doesn't exist in base table
            if (!Schema::hasColumn('budgets', 'job_id')) {
                 $table->foreignId('job_id')->nullable()->after('sub_department_id')->constrained('jobs')->onDelete('cascade');
            }
        });

        // 3. Drop job_coas table
        Schema::dropIfExists('job_coas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate job_coas
        Schema::create('job_coas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        // Revert jobs
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->foreignId('job_coa_id')->nullable()->constrained('job_coas')->onDelete('cascade');
        });

        // Revert budgets
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->dropColumn('job_id');
            $table->foreignId('job_coa_id')->nullable()->constrained('job_coas')->onDelete('cascade');
        });
    }
};
