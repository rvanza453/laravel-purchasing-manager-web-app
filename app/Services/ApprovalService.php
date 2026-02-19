<?php

namespace App\Services;

use App\Enums\PrStatus;
use App\Models\PurchaseRequest;
use App\Models\PrApproval;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function approve(PrApproval $approval, string $remarks = null, array $adjustedQuantities = null)
    {
         return DB::transaction(function () use ($approval, $remarks, $adjustedQuantities) {
            $approval->update([
                'status' => PrStatus::APPROVED->value,
                'approved_at' => now(),
                'remarks' => $remarks,
                'adjusted_quantities' => $adjustedQuantities
            ]);

            // Check if all approvals for this PR are done?
            // Or move to next level?
            
            $pr = $approval->purchaseRequest;
            
            // Check if there is a higher level pending
            $nextApproval = PrApproval::where('purchase_request_id', $pr->id)
                ->where('level', '>', $approval->level)
                ->orderBy('level')
                ->first();

            if (!$nextApproval) {
                // All approved
                $pr->update(['status' => PrStatus::APPROVED->value]);
            } else {
                if ($pr->status !== PrStatus::PENDING->value) {
                     $pr->update(['status' => PrStatus::PENDING->value]);
                }
            }

            // Clear approver filter cache
            $this->clearApproverCache();
            
            return true;
         });
    }

    public function reject(PrApproval $approval, string $remarks)
    {
        return DB::transaction(function () use ($approval, $remarks) {
            $approval->update([
                'status' => PrStatus::REJECTED->value,
                'approved_at' => now(),
                'remarks' => $remarks
            ]);

            // Mark PR as Rejected
            $approval->purchaseRequest->update(['status' => PrStatus::REJECTED->value]);

            // Clear approver filter cache
            $this->clearApproverCache();
            
            return true;
        });
    }

    public function hold(PrApproval $approval, string $remarks)
    {
        return DB::transaction(function () use ($approval, $remarks) {
            $approval->update([
                'status' => PrStatus::ON_HOLD->value,
                'approved_at' => now(), // Time of action
                'remarks' => $remarks
            ]);

            // Mark PR as On Hold (Global Status)
            $approval->purchaseRequest->update(['status' => PrStatus::ON_HOLD->value]);

            // Clear approver filter cache
            $this->clearApproverCache();
            
            return true;
        });
    }
    
    public function fullApprove(PurchaseRequest $pr, $adminId)
    {
        return DB::transaction(function () use ($pr, $adminId) {
            // 1. Get all approvals that are NOT approved yet
            $pendingApprovals = $pr->approvals()
                ->where('status', '!=', PrStatus::APPROVED->value)
                ->get();

            foreach ($pendingApprovals as $approval) {
                $approval->update([
                    'status' => PrStatus::APPROVED->value,
                    'approved_at' => now(),
                    'remarks' => 'Full Approved by Super Admin',
                ]);
            }

            // 2. Set PR Status to Approved
            $pr->update(['status' => PrStatus::APPROVED->value]);

            // 3. Clear Cache
            $this->clearApproverCache();

            return true;
        });
    }
    
    /**
     * Clear approver filter cache
     */
    protected function clearApproverCache()
    {
        \Cache::forget('pr_current_approvers_*');
    }
}
