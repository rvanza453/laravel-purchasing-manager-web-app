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
                // Still pending next level
                // Optional: Notify next approver
            }

            return true;
         });
    }

    public function reject(PrApproval $approval, string $remarks)
    {
        return DB::transaction(function () use ($approval, $remarks) {
            $approval->update([
                'status' => PrStatus::REJECTED->value,
                'approved_at' => now(), // Rejected time
                'remarks' => $remarks
            ]);

            // Mark PR as Rejected
            $approval->purchaseRequest->update(['status' => PrStatus::REJECTED->value]);

            return true;
        });
    }
}
