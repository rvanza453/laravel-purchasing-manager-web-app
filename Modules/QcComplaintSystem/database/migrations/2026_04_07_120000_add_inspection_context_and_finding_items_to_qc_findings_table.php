<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qc_findings', function (Blueprint $table) {
            $table->json('inspection_context')->nullable()->after('location');
            $table->json('finding_items')->nullable()->after('inspection_context');
        });
    }

    public function down(): void
    {
        Schema::table('qc_findings', function (Blueprint $table) {
            $table->dropColumn(['inspection_context', 'finding_items']);
        });
    }
};