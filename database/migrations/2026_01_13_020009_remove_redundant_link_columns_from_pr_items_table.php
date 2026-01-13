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
        Schema::table('pr_items', function (Blueprint $table) {
            $table->dropColumn([
                'product_link',
                'product_image',
                'product_name_from_link',
                'product_price_from_link'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pr_items', function (Blueprint $table) {
            $table->text('product_link')->nullable();
            $table->string('product_image')->nullable();
            $table->string('product_name_from_link')->nullable();
            $table->decimal('product_price_from_link', 15, 2)->nullable();
        });
    }
};
