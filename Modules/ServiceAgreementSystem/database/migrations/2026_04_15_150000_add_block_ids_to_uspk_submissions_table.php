<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('uspk_submissions', 'block_ids')) {
            Schema::table('uspk_submissions', function (Blueprint $table) {
                $table->json('block_ids')->nullable()->after('block_id');
            });
        }

        DB::table('uspk_submissions')
            ->select(['id', 'block_id', 'block_ids'])
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    if (!empty($row->block_ids)) {
                        continue;
                    }

                    if (!empty($row->block_id)) {
                        DB::table('uspk_submissions')
                            ->where('id', $row->id)
                            ->update(['block_ids' => json_encode([(int) $row->block_id])]);
                    }
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('uspk_submissions', 'block_ids')) {
            Schema::table('uspk_submissions', function (Blueprint $table) {
                $table->dropColumn('block_ids');
            });
        }
    }
};
