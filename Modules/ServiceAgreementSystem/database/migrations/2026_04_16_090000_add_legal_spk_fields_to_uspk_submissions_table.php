<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uspk_submissions', function (Blueprint $table) {
            $table->string('legal_spk_document_path')->nullable()->after('submitted_at');
            $table->foreignId('legal_spk_uploaded_by')->nullable()->after('legal_spk_document_path')->constrained('users')->nullOnDelete();
            $table->timestamp('legal_spk_uploaded_at')->nullable()->after('legal_spk_uploaded_by');
            $table->text('legal_spk_notes')->nullable()->after('legal_spk_uploaded_at');
        });
    }

    public function down(): void
    {
        Schema::table('uspk_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('legal_spk_uploaded_by');
            $table->dropColumn([
                'legal_spk_document_path',
                'legal_spk_uploaded_at',
                'legal_spk_notes',
            ]);
        });
    }
};
