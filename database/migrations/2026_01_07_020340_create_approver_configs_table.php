<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approver_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete(); // Was category_id
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            $table->string('role_name'); 
            $table->integer('level'); 
            $table->timestamps();

            $table->unique(['department_id', 'level']); 
            $table->index(['department_id', 'level']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approver_configs');
    }
};
