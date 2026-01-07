<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            
            // Custom fields that don't depend on other tables yet
             $table->boolean('is_active')->default(true);
             $table->string('position')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
