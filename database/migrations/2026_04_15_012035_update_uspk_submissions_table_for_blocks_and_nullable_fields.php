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
        Schema::table('uspk_submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('block_id')->nullable()->change();
            $table->json('block_ids')->nullable()->after('sub_department_id');
            $table->decimal('estimated_value', 15, 2)->nullable()->change();
            $table->integer('estimated_duration')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uspk_submissions', function (Blueprint $table) {
            $table->dropColumn('block_ids');
        });
    }
};
