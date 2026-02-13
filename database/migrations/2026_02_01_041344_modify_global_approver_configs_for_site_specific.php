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
        Schema::table('global_approver_configs', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            // Drop unique constraint on level because different sites might have different people at the same level
            // Note: The constraint name is usually global_approver_configs_level_unique
            $table->dropUnique(['level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_approver_configs', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
            // Restore unique constraint (might fail if duplicates exist now, but standard rollback flow)
            $table->unique('level');
        });
    }
};
