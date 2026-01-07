<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pr_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users');
            $table->integer('level');
            $table->string('role_name');
            $table->string('status')->index(); // Pending, Approved, Rejected
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable(); // Rejection reason, etc.
            $table->timestamps();

            $table->index(['approver_id', 'status']); // For "My Pending Approvals"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pr_approvals');
    }
};
