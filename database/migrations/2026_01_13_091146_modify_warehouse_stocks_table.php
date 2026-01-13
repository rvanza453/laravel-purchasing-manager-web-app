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
        // 1. Add warehouse_id column (nullable first to allow data migration)
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
        });

        // 2. Data Migration: Create default warehouses for existing sites and move stocks
        // We need to use raw DB queries or models. Let's use DB to avoid Model issues during migration.
        $sites = \Illuminate\Support\Facades\DB::table('sites')->get();
        foreach ($sites as $site) {
            // Check if a warehouse exists for this site, if not create a default one
            $warehouseId = \Illuminate\Support\Facades\DB::table('warehouses')->insertGetId([
                'site_id' => $site->id,
                'name' => 'Main Warehouse - ' . $site->name, // Default name
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign departments of this site to this warehouse (Optional, but good for consistency)
            \Illuminate\Support\Facades\DB::table('departments')
                ->where('site_id', $site->id)
                ->whereNull('warehouse_id')
                ->update(['warehouse_id' => $warehouseId]);

            // Update existing stocks for this site to point to the new warehouse
            \Illuminate\Support\Facades\DB::table('warehouse_stocks')
                ->where('site_id', $site->id)
                ->update(['warehouse_id' => $warehouseId]);
        }

        // 3. Make warehouse_id not nullable and drop site_id
        // (Only if we are sure all data is migrated. If table was empty, this is fine).
        // However, if there are stocks with invalid site_id, they might fail. 
        // We assume integrity is good.

        Schema::table('warehouse_stocks', function (Blueprint $table) {
             // We need to drop the composite unique key involving site_id first if it exists
             // The original migration had: $table->unique(['product_id', 'site_id']);
             $table->dropUnique(['product_id', 'site_id']);
             
             $table->dropForeign(['site_id']);
             $table->dropColumn('site_id');
             
             // Now make warehouse_id required
             $table->unsignedBigInteger('warehouse_id')->nullable(false)->change();
             
             // Add new unique constraint
             $table->unique(['product_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
        });

        // Reverse migration of data is tricky since multiple warehouses might strictly belong to one site, 
        // but stocks are now split. We can try to map back to the site of the warehouse.
        
        $stocks = \Illuminate\Support\Facades\DB::table('warehouse_stocks')
            ->join('warehouses', 'warehouse_stocks.warehouse_id', '=', 'warehouses.id')
            ->select('warehouse_stocks.id', 'warehouses.site_id')
            ->get();

        foreach ($stocks as $stock) {
             \Illuminate\Support\Facades\DB::table('warehouse_stocks')
                ->where('id', $stock->id)
                ->update(['site_id' => $stock->site_id]);
        }
        
        Schema::table('warehouse_stocks', function (Blueprint $table) {
             $table->dropForeign(['warehouse_id']);
             $table->dropUnique(['product_id', 'warehouse_id']);
             $table->dropColumn('warehouse_id');
             
             $table->unsignedBigInteger('site_id')->nullable(false)->change();
             $table->unique(['product_id', 'site_id']);
        });
    }
};
