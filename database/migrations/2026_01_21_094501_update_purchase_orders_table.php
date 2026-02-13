<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Remove old purchase_request_id constraint (will track via po_items)
            $table->dropForeign(['purchase_request_id']);
            
            // Keep purchase_request_id for reference but make it nullable
            $table->foreignId('purchase_request_id')->nullable()->change();
            
            // Add new fields
            $table->date('po_date')->after('po_number'); // Date when PO is created
            $table->date('delivery_date')->nullable()->after('po_date'); // Expected delivery
            $table->string('pr_number')->nullable()->after('purchase_request_id'); // Reference to PR
            $table->date('pr_date')->nullable()->after('pr_number'); // PR date
            
            // Discount and tax fields
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('final_amount');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percentage');
            $table->decimal('dpp_lainnya', 15, 2)->default(0)->after('discount_amount'); // DPP Lainnya
            $table->decimal('dpp', 15, 2)->default(0)->after('dpp_lainnya'); // Dasar Pengenaan Pajak
            $table->decimal('ppn_percentage', 5, 2)->default(12)->after('dpp'); // PPN %
            $table->decimal('ppn_amount', 15, 2)->default(0)->after('ppn_percentage'); // PPN Amount
            
            // Notes/Terms
            $table->text('notes')->nullable()->after('ppn_amount');
            
            // Remove issued_date (replaced by po_date)
            $table->dropColumn('issued_date');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Restore original structure
            $table->date('issued_date')->nullable();
            
            $table->dropColumn([
                'po_date',
                'delivery_date',
                'pr_number',
                'pr_date',
                'discount_percentage',
                'discount_amount',
                'dpp_lainnya',
                'dpp',
                'ppn_percentage',
                'ppn_amount',
                'notes'
            ]);
            
            // Restore foreign key
            $table->foreignId('purchase_request_id')->change();
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests');
        });
    }
};
