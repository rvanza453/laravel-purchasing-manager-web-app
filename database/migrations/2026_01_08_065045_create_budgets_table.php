<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_department_id')->constrained()->cascadeOnDelete();
            $table->string('category'); // Product Category
            $table->decimal('amount', 15, 2)->default(0); // Monthly/Yearly Limit
            $table->integer('year')->default(date('Y'));
            $table->timestamps();

            // Unique budget per sub-dept, category, and year
            $table->unique(['sub_department_id', 'category', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
