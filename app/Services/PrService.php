<?php

namespace App\Services;

use App\Enums\PrStatus;
use App\Models\PurchaseRequest;
use App\Models\PrItem;
use App\Models\PrApproval;
use App\Models\ApproverConfig;
use App\Models\Department;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class PrService
{
    public function createPr(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            // 1. Generate PR Number
            $dept = Department::find($data['department_id']);
            $year = date('Y');
            $month = date('m');
            $count = PurchaseRequest::whereYear('created_at', $year)->count() + 1;
            $prNumber = sprintf("PR/%s/%s/%s/%04d", $dept->code, $year, $month, $count);

            // 2. Create PR Record
            $pr = PurchaseRequest::create([
                'user_id' => auth()->id(),
                'department_id' => $data['department_id'],
                'pr_number' => $prNumber,
                'request_date' => $data['request_date'],
                'description' => $data['description'],
                'status' => PrStatus::PENDING->value,
            ]);

            // 3. Create Items
            $totalCost = 0;
            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['price_estimation'];
                
                // Fetch product details if product_id is present
                $productName = $item['item_name'] ?? null;
                $unit = $item['unit'] ?? null;
                
                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $productName = $product->name; // Snapshot name
                        $unit = $product->unit;
                    }
                }

                $pr->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'item_name' => $productName,
                    'quantity' => $item['quantity'],
                    'unit' => $unit,
                    'price_estimation' => $item['price_estimation'],
                    'subtotal' => $subtotal,
                ]);
                $totalCost += $subtotal;
            }

            $pr->update(['total_estimated_cost' => $totalCost]);

            // 4. Generate Initial Approvals based on Department
            $this->generateApprovals($pr);

            return $pr;
        });
    }

    private function generateApprovals(PurchaseRequest $pr)
    {
        $approverConfigs = ApproverConfig::where('department_id', $pr->department_id)
            ->orderBy('level')
            ->get();

        if ($approverConfigs->isEmpty()) {
            // Auto Approve if no config? Or stuck?
            // For now, let's keep it pending or maybe set a fallback
            return; 
        }

        foreach ($approverConfigs as $config) {
            $pr->approvals()->create([
                'approver_id' => $config->user_id,
                'level' => $config->level,
                'role_name' => $config->role_name,
                'status' => PrStatus::PENDING->value, // All pending initially, or sequential?
                // For sequential, only level 1 is "Pending", others "Queued"? 
                // Let's keep simple: "Pending" but UI only shows to Level 1 first.
            ]);
        }
    }
}
