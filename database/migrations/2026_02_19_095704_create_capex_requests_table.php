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
        Schema::create('capex_requests', function (Blueprint $table) {
            $table->id();
            $table->string('capex_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Requester
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('capex_budget_id')->constrained('capex_budgets')->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->json('questionnaire_answers')->nullable(); // Stores answers to 6 static questions
            $table->string('status')->default('Pending'); // Draft, Pending, Approved, Rejected, PrCreated
            $table->integer('current_step')->default(1); // 1-13
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capex_requests');
    }
};
