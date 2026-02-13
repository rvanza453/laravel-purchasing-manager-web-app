<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('pr_item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Approved quantity
            $table->string('unit');
            $table->decimal('unit_price', 15, 2); // User input price
            $table->decimal('subtotal', 15, 2); // quantity * unit_price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_items');
    }
};
