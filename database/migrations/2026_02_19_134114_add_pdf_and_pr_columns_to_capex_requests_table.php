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
        Schema::table('capex_requests', function (Blueprint $table) {
            $table->string('signed_file_path')->nullable()->after('status');
            $table->boolean('is_verified')->default(false)->after('signed_file_path');
            $table->unsignedBigInteger('pr_id')->nullable()->after('is_verified');
            // Assuming purchase_requests table exists and constrained, but to be safe we can just make it bigInteger
            // $table->foreign('pr_id')->references('id')->on('purchase_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capex_requests', function (Blueprint $table) {
            $table->dropColumn(['signed_file_path', 'is_verified', 'pr_id']);
        });
    }
};
