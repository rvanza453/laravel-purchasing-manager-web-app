<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., Stasiun Press
            $table->string('code')->nullable(); // e.g., PRESS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_departments');
    }
};
