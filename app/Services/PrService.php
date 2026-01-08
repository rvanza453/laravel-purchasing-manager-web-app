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
                'sub_department_id' => $data['sub_department_id'] ?? null, // Add sub_dept
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
                    'specification' => $item['specification'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $unit,
                    'price_estimation' => $item['price_estimation'],
                    'subtotal' => $subtotal,
                    'manual_category' => $item['manual_category'] ?? null,
                ]);
                $totalCost += $subtotal;
            }

            $pr->update(['total_estimated_cost' => $totalCost]);

            // 4. Generate Initial Approvals based on Department
            $this->generateApprovals($pr);

            // 5. Budget is now calculated heavily on validation (PrController), 
            // no need to decrement a 'budget' column on department table anymore 
            // as we use the 'budgets' table limits.
            
            return $pr;
        });
    }

    private function generateApprovals(PurchaseRequest $pr)
    {
        $approverConfigs = ApproverConfig::where('department_id', $pr->department_id)
            ->orderBy('level')
            ->get();

        $maxLevel = 0;

        foreach ($approverConfigs as $config) {
            $pr->approvals()->create([
                'approver_id' => $config->user_id,
                'level' => $config->level,
                'role_name' => $config->role_name,
                'status' => PrStatus::PENDING->value,
            ]);
            $maxLevel = max($maxLevel, $config->level);
        }

        // Check for Global Approvals (HO)
        if ($pr->department->use_global_approval) {
            $globalApprovers = \App\Models\GlobalApproverConfig::orderBy('level')->get();
            
            foreach ($globalApprovers as $globalConfig) {
                $newLevel = $maxLevel + $globalConfig->level;

                $pr->approvals()->create([
                    'approver_id' => $globalConfig->user_id,
                    'level' => $newLevel,
                    'role_name' => $globalConfig->role_name,
                    'status' => PrStatus::PENDING->value,
                ]);
            }
        }
    }
}
