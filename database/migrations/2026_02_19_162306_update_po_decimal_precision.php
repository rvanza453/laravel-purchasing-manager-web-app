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
        Schema::table('po_items', function (Blueprint $table) {
            $table->decimal('unit_price', 30, 10)->change();
            $table->decimal('subtotal', 30, 10)->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('final_amount', 30, 10)->change();
            $table->decimal('discount_amount', 30, 10)->change();
            $table->decimal('dpp_lainnya', 30, 10)->change();
            $table->decimal('dpp', 30, 10)->change();
            $table->decimal('ppn_amount', 30, 10)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('subtotal', 15, 2)->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('final_amount', 15, 2)->change();
            $table->decimal('discount_amount', 15, 2)->change();
            $table->decimal('dpp_lainnya', 15, 2)->change();
            $table->decimal('dpp', 15, 2)->change();
            $table->decimal('ppn_amount', 15, 2)->change();
        });
    }
};
