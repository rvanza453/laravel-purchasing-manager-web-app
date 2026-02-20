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
        Schema::create('capex_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capex_request_id')->constrained('capex_requests')->cascadeOnDelete();
            $table->integer('column_index'); // 1-13
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete(); // Who actually acted
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->text('remarks')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->index(['capex_request_id', 'column_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capex_approvals');
    }
};
