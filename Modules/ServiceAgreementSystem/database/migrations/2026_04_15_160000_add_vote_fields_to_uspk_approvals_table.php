<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uspk_approvals', function (Blueprint $table) {
            $table->foreignId('vote_tender_id')->nullable()->after('schema_id')->constrained('uspk_tenders')->nullOnDelete();
            $table->decimal('vote_tender_value', 15, 2)->nullable()->after('vote_tender_id');
            $table->integer('vote_tender_duration')->nullable()->after('vote_tender_value');
            $table->text('vote_tender_description')->nullable()->after('vote_tender_duration');
        });
    }

    public function down(): void
    {
        Schema::table('uspk_approvals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vote_tender_id');
            $table->dropColumn([
                'vote_tender_value',
                'vote_tender_duration',
                'vote_tender_description',
            ]);
        });
    }
};
