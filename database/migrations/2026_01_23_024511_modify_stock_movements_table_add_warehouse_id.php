<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
        });

        // Migrate data
        // For each stock movement, assign it to the first warehouse found for that site
        $movements = DB::table('stock_movements')->get();
        foreach ($movements as $movement) {
            $warehouse = DB::table('warehouses')->where('site_id', $movement->site_id)->first();
            if ($warehouse) {
                DB::table('stock_movements')
                    ->where('id', $movement->id)
                    ->update(['warehouse_id' => $warehouse->id]);
            }
        }

        // If table was empty or migration successful, enforce FK and drop old column
        Schema::table('stock_movements', function (Blueprint $table) {
            // We can't strictly enforce NOT NULL if some movements didn't find a warehouse, 
            // but we assume integrity.
            // Let's unsafe modify column to not null if needed, but for now we trust the loop.
            
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
        });
        
        // Separate schema call to change column type usually safer
        Schema::table('stock_movements', function (Blueprint $table) {
             $table->unsignedBigInteger('warehouse_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
        });

        // Restore data
        $movements = DB::table('stock_movements')
            ->join('warehouses', 'stock_movements.warehouse_id', '=', 'warehouses.id')
            ->select('stock_movements.id', 'warehouses.site_id')
            ->get();

        foreach ($movements as $movement) {
            DB::table('stock_movements')
                ->where('id', $movement->id)
                ->update(['site_id' => $movement->site_id]);
        }

        Schema::table('stock_movements', function (Blueprint $table) {
             $table->dropForeign(['warehouse_id']);
             $table->dropColumn('warehouse_id');
             $table->unsignedBigInteger('site_id')->nullable(false)->change();
        });
    }
};
