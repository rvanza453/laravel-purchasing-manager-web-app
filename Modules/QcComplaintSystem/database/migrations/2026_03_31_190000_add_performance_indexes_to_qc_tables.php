<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qc_findings', function (Blueprint $table) {
            $this->addIndexIfMissing('qc_findings', ['status', 'finding_date'], 'qc_findings_status_finding_date_idx');
            $this->addIndexIfMissing('qc_findings', ['department_id', 'status', 'finding_date'], 'qc_findings_dept_status_date_idx');
            $this->addIndexIfMissing('qc_findings', ['kategori', 'sub_kategori'], 'qc_findings_kategori_sub_kategori_idx');
            $this->addIndexIfMissing('qc_findings', ['needs_resubmission', 'status'], 'qc_findings_resubmission_status_idx');
            $this->addIndexIfMissing('qc_findings', ['sub_department_id', 'status'], 'qc_findings_sub_department_status_idx');
            $this->addIndexIfMissing('qc_findings', ['block_id', 'status'], 'qc_findings_block_status_idx');
        });

        Schema::table('qc_finding_approval_steps', function (Blueprint $table) {
            $this->addIndexIfMissing('qc_finding_approval_steps', ['approver_user_id', 'status', 'created_at'], 'qc_fas_approver_status_created_idx');
            $this->addIndexIfMissing('qc_finding_approval_steps', ['qc_finding_id', 'status', 'level'], 'qc_fas_finding_status_level_idx');
        });

        Schema::table('qc_finding_comments', function (Blueprint $table) {
            $this->addIndexIfMissing('qc_finding_comments', ['qc_finding_id', 'parent_comment_id', 'created_at'], 'qc_finding_comments_thread_created_idx');
            $this->addIndexIfMissing('qc_finding_comments', ['parent_comment_id', 'created_at'], 'qc_finding_comments_parent_created_idx');
        });

        Schema::table('qc_finding_completion_evidences', function (Blueprint $table) {
            $this->addIndexIfMissing('qc_finding_completion_evidences', ['qc_finding_id', 'id'], 'qc_fce_finding_id_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('qc_finding_completion_evidences', function (Blueprint $table) {
            $this->dropIndexIfExists('qc_finding_completion_evidences', 'qc_fce_finding_id_id_idx');
        });

        Schema::table('qc_finding_comments', function (Blueprint $table) {
            $this->dropIndexIfExists('qc_finding_comments', 'qc_finding_comments_parent_created_idx');
            $this->dropIndexIfExists('qc_finding_comments', 'qc_finding_comments_thread_created_idx');
        });

        Schema::table('qc_finding_approval_steps', function (Blueprint $table) {
            $this->dropIndexIfExists('qc_finding_approval_steps', 'qc_fas_finding_status_level_idx');
            $this->dropIndexIfExists('qc_finding_approval_steps', 'qc_fas_approver_status_created_idx');
        });

        Schema::table('qc_findings', function (Blueprint $table) {
            $this->dropIndexIfExists('qc_findings', 'qc_findings_block_status_idx');
            $this->dropIndexIfExists('qc_findings', 'qc_findings_sub_department_status_idx');
            $this->dropIndexIfExists('qc_findings', 'qc_findings_resubmission_status_idx');
            $this->dropIndexIfExists('qc_findings', 'qc_findings_kategori_sub_kategori_idx');
            $this->dropIndexIfExists('qc_findings', 'qc_findings_dept_status_date_idx');
            $this->dropIndexIfExists('qc_findings', 'qc_findings_status_finding_date_idx');
        });
    }

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
            $blueprint->dropIndex($indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        $results = DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$database, $table, $indexName]
        );

        return !empty($results);
    }
};
