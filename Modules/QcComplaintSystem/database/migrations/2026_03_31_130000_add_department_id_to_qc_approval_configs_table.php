<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qc_approval_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('qc_approval_configs', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('departments')
                    ->nullOnDelete();

                $table->index(['department_id', 'id'], 'qc_approval_configs_department_id_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('qc_approval_configs', function (Blueprint $table) {
            if (Schema::hasColumn('qc_approval_configs', 'department_id')) {
                $table->dropIndex('qc_approval_configs_department_id_idx');
                $table->dropConstrainedForeignId('department_id');
            }
        });
    }
};
