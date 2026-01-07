<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('site_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // Who moved it
            $table->string('type'); // IN, OUT, ADJUST
            $table->integer('quantity'); // can be negative for OUT? Or strict column?
            // Usually strict positive qty, and type determines logic.
            
            $table->string('reference_number')->nullable(); // PO Number or PR Number
            $table->text('remarks')->nullable();
            $table->timestamp('date');
            $table->timestamps();
            
            $table->index(['site_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
