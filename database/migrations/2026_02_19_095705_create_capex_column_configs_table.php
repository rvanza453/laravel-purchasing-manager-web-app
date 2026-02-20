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
        Schema::create('capex_column_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('column_index'); // 1-13
            $table->string('label'); // e.g., "Prepared By", "Approved By"
            $table->string('approver_role')->nullable(); // Role name
            $table->foreignId('approver_user_id')->nullable()->constrained('users')->nullOnDelete(); // Specific user
            $table->boolean('is_digital')->default(true); // false = wet signature
            $table->timestamps();

            $table->unique('column_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capex_column_configs');
    }
};
