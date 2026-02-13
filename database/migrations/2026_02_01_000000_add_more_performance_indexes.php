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
        // Products: Indexing code and name for faster searching during PR/PO creation
        Schema::table('products', function (Blueprint $table) {
            $table->index('code', 'idx_products_code');
            $table->index('name', 'idx_products_name');
        });

        // Vendors: Indexing name for faster searching
        Schema::table('vendors', function (Blueprint $table) {
            $table->index('name', 'idx_vendors_name');
        });

        // Purchase Orders: Indexing vendor_id for filtering, created_at for sorting/reporting
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('vendor_id', 'idx_po_vendor_id');
            $table->index('created_at', 'idx_po_created_at');
        });

        // Stock Movements: Indexing created_at for history logs and product_id for specific item history
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index('created_at', 'idx_stock_movements_created_at');
            $table->index('product_id', 'idx_stock_movements_product_id');
        });
        
        // Activity Logs: Ensure created_at is indexed for log viewing
         if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                try {
                    $table->index('created_at');
                } catch (\Throwable $e) { // Use Throwable to catch all errors including missing drivers
                    // Index likely already exists or driver issue, safe to ignore for optional performance index
                }
            });
         }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_code');
            $table->dropIndex('idx_products_name');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex('idx_vendors_name');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex('idx_po_vendor_id');
            $table->dropIndex('idx_po_created_at');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex('idx_stock_movements_created_at');
            $table->dropIndex('idx_stock_movements_product_id');
        });
        
        if (Schema::hasTable('activity_logs')) {
             Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
             });
        }
    }
};
