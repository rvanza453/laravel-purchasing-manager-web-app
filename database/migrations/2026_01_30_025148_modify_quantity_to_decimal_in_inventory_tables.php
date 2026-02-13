<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw SQL to avoid doctrine/dbal dependency issues
        // MySQL Syntax: ALTER TABLE table MODIFY COLUMN column type
        DB::statement('ALTER TABLE stock_movements MODIFY COLUMN quantity decimal(15,4)');
        DB::statement('ALTER TABLE warehouse_stocks MODIFY COLUMN quantity decimal(15,4)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE stock_movements MODIFY COLUMN quantity integer');
        DB::statement('ALTER TABLE warehouse_stocks MODIFY COLUMN quantity integer');
    }
};
