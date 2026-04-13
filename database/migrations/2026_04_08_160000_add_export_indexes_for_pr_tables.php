<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('purchase_requests')) {
            $this->addIndexIfMissing('purchase_requests', 'pr_status_request_date_idx', ['status', 'request_date']);
            $this->addIndexIfMissing('purchase_requests', 'pr_dept_subdept_idx', ['department_id', 'sub_department_id']);
            $this->addIndexIfMissing('purchase_requests', 'pr_created_at_idx', ['created_at']);
        }

        if (Schema::hasTable('pr_approvals')) {
            $this->addIndexIfMissing('pr_approvals', 'pra_pr_status_level_idx', ['purchase_request_id', 'status', 'level']);
            $this->addIndexIfMissing('pr_approvals', 'pra_pr_status_approvedat_idx', ['purchase_request_id', 'status', 'approved_at']);
        }

        if (Schema::hasTable('pr_items')) {
            $this->addIndexIfMissing('pr_items', 'pri_purchase_request_idx', ['purchase_request_id']);
        }

        if (Schema::hasTable('po_items')) {
            $this->addIndexIfMissing('po_items', 'poi_pr_item_idx', ['pr_item_id']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('purchase_requests')) {
            $this->dropIndexIfExists('purchase_requests', 'pr_status_request_date_idx');
            $this->dropIndexIfExists('purchase_requests', 'pr_dept_subdept_idx');
            $this->dropIndexIfExists('purchase_requests', 'pr_created_at_idx');
        }

        if (Schema::hasTable('pr_approvals')) {
            $this->dropIndexIfExists('pr_approvals', 'pra_pr_status_level_idx');
            $this->dropIndexIfExists('pr_approvals', 'pra_pr_status_approvedat_idx');
        }

        if (Schema::hasTable('pr_items')) {
            $this->dropIndexIfExists('pr_items', 'pri_purchase_request_idx');
        }

        if (Schema::hasTable('po_items')) {
            $this->dropIndexIfExists('po_items', 'poi_pr_item_idx');
        }
    }

    private function addIndexIfMissing(string $table, string $indexName, array $columns): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName) {
            $tableBlueprint->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
            $tableBlueprint->dropIndex($indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $dbName = DB::getDatabaseName();

        $result = DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$dbName, $table, $indexName]
        );

        return !empty($result);
    }
};
