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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('vendor_postal_code')->nullable()->after('vendor_address');
            $table->string('vendor_contact_person')->nullable()->after('vendor_phone'); // UP / Admin
            $table->string('vendor_contact_phone')->nullable()->after('vendor_contact_person');
            $table->string('vendor_email')->nullable()->after('vendor_contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //
        });
    }
};
