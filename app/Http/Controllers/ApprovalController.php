<?php

namespace App\Http\Controllers;

use App\Models\PrApproval;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index()
    {
        // Admin can see all pending/on-hold approvals
        // Regular users see only their assigned approvals which are "Current Turn"
        
        $query = PrApproval::whereIn('status', ['Pending', 'On Hold'])
            ->with(['purchaseRequest.user', 'purchaseRequest.department', 'purchaseRequest.items.job', 'purchaseRequest.approvals']);
        
        if (!auth()->user()->hasRole('admin')) {
            $query->where('approver_id', auth()->id());
        }
        
        $approvals = $query->orderBy('created_at', 'asc')->get();

        // Filter: Only show if this is the LOWEST Pending/OnHold level for the PR
        // If Admin, maybe show all? The user request implies strict order.
        // Let's enforce strict order even for what "Inbox" shows, but Admin might have a separate view usually.
        // Assuming strict compliance:
        
        $filtered = $approvals->filter(function ($approval) {
            // If the PR is globally rejected or cancelled, don't show (though status check above might cover IDK)
            // But individual approval is Pending/OnHold.
            
            // Get all approvals for this PR that are not approved/rejected yet
            // actually we just need to see if there is any level < this level that is NOT Approved
            
            $previousLevelsNotApproved = $approval->purchaseRequest->approvals
                ->filter(function ($other) use ($approval) {
                    return $other->level < $approval->level && $other->status !== \App\Enums\PrStatus::APPROVED->value;
                });

            return $previousLevelsNotApproved->isEmpty();
        });

        return view('approval.index', ['approvals' => $filtered]);
    }

    private function enforceSequentialApproval(PrApproval $approval)
    {
        // Allow Admin to bypass sequential enforcement
        if (auth()->user()->hasRole('admin')) {
            return;
        }
        
        // User requested strict order: "jika dari atas belum approve, maka tidak akan muncul dibawahnya"
        // However, if THIS approval is On Hold, the approver should be able to approve/reject it
        // So we check for LOWER levels that are not Approved AND not the current approval
        
        $hasPendingLowerLevel = PrApproval::where('purchase_request_id', $approval->purchase_request_id)
            ->where('level', '<', $approval->level)
            ->whereNotIn('status', [\App\Enums\PrStatus::APPROVED->value])
            ->exists();

        if ($hasPendingLowerLevel) {
            abort(403, 'Previous approval levels must be completed first.');
        }
    }

    public function approve(Request $request, PrApproval $approval)
    {
        $this->enforceSequentialApproval($approval);

        // Ensure user owns this approval OR is Admin
        if ($approval->approver_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Check if user is an HO Approver (Global Approver) or Admin
        $isHO = auth()->user()->hasRole('admin') || \App\Models\GlobalApproverConfig::where('user_id', auth()->id())->exists();

        // Validate adjusted quantities if provided (only for HO approvers)
        $validated = $request->validate([
            'remarks' => 'nullable|string',
            'adjusted_quantities' => 'nullable|array',
            'adjusted_quantities.*' => 'nullable|numeric|min:0',
        ]);

        $adjustedQuantities = $request->input('adjusted_quantities');
        
        // If not HO, ignore adjusted quantities to prevent unauthorized changes
        if (!$isHO) {
            $adjustedQuantities = null;
        }

        $this->approvalService->approve(
            $approval, 
            $request->input('remarks'),
            $adjustedQuantities
        );

        return redirect()->route('approval.index')->with('success', 'PR Approved successfully.');
    }

    public function hold(Request $request, PrApproval $approval)
    {
        $this->enforceSequentialApproval($approval);

        // Ensure user owns this approval OR is Admin
        if ($approval->approver_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate(['remarks' => 'required|string']);

        $this->approvalService->hold($approval, $request->input('remarks'));

        return redirect()->route('approval.index')->with('success', 'PR placed On Hold.');
    }

    public function reject(Request $request, PrApproval $approval)
    {
        $this->enforceSequentialApproval($approval);

        if ($approval->approver_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        
        $request->validate(['remarks' => 'required|string']);

        $this->approvalService->reject($approval, $request->input('remarks'));

        return redirect()->route('approval.index')->with('success', 'PR Rejected.');
    }
}
