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
        // 1. Add budget_type to departments
        Schema::table('departments', function (Blueprint $table) {
            $table->string('budget_type')->default('station')->after('coa'); 
            // Values: 'station', 'job_coa'
        });

        // 2. Create job_coas table
        Schema::create('job_coas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. 600-01
            $table->string('name'); // e.g. "Biaya Panen"
            $table->foreignId('sub_department_id')->constrained('sub_departments')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Create jobs table
        
        // Fix: Drop dependants first
        if (Schema::hasColumn('budgets', 'job_id')) {
            Schema::table('budgets', function (Blueprint $table) {
               $table->dropForeign(['job_id']);
            });
        }
        
        Schema::dropIfExists('jobs');
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Potong Buah Blok A"
            $table->foreignId('job_coa_id')->constrained('job_coas')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Add job_coa_id to budgets table
        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('job_coa_id')->nullable()->after('sub_department_id')->constrained('job_coas')->onDelete('cascade');
            // We make category nullable or strictly use one or the other in code logic
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign(['job_coa_id']);
            $table->dropColumn('job_coa_id');
        });

        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_coas');

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('budget_type');
        });
    }
};
