<?php

namespace App\Http\Controllers;

use App\Models\PrApproval;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $approvalService;
    protected $fonnteService;

    public function __construct(ApprovalService $approvalService, \App\Services\FonnteService $fonnteService)
    {
        $this->approvalService = $approvalService;
        $this->fonnteService = $fonnteService;
    }

    public function index()
    {
        // Admin can see all pending/on-hold approvals
        // Regular users see only their assigned approvals which are "Current Turn"
        
        $query = PrApproval::whereIn('status', ['Pending', 'On Hold'])
            ->with(['purchaseRequest.user', 'purchaseRequest.department', 'purchaseRequest.items', 'purchaseRequest.approvals']);
        
        if (!auth()->user()->hasRole('Admin')) {
            $query->where('approver_id', auth()->id());
        }
        
        $approvals = $query->orderBy('created_at', 'asc')->get();
        
        $filtered = $approvals->filter(function ($approval) {
            $allPreviousApproved = $approval->purchaseRequest->approvals
                ->filter(function ($other) use ($approval) {
                    return $other->level < $approval->level;
                })
                ->every(function ($other) {
                    return $other->status === \App\Enums\PrStatus::APPROVED->value;
                });

            return $allPreviousApproved && in_array($approval->status, ['Pending', 'On Hold']);
        });

        return view('approval.index', ['approvals' => $filtered]);
    }

    private function enforceSequentialApproval(PrApproval $approval)
    {
        // Allow Admin to bypass sequential enforcement
        if (auth()->user()->hasRole('Admin')) {
            return;
        }
        
        // Cek apakah ada level di bawahnya yang belum Approved (Status bukan Approved)
        $hasPendingLowerLevel = PrApproval::where('purchase_request_id', $approval->purchase_request_id)
            ->where('level', '<', $approval->level)
            ->whereNotIn('status', [\App\Enums\PrStatus::APPROVED->value]) 
            ->exists();

        if ($hasPendingLowerLevel) {
            abort(403, 'Approval level sebelumnya belum selesai. Harap tunggu giliran.');
        }
    }

    public function approve(Request $request, PrApproval $approval)
    {
        \Illuminate\Support\Facades\Log::info('Approval Attempt', [
            'user_id' => auth()->id(),
            'approver_id' => $approval->approver_id,
            'approval_id' => $approval->id,
            'role' => auth()->user()->getRoleNames()
        ]);

        $this->enforceSequentialApproval($approval);

        // Ensure user owns this approval OR is Admin
        // Use loose comparison (!=) to handle potential int/string mismatch
        if ($approval->approver_id != auth()->id() && !auth()->user()->hasRole('Admin')) { 
             abort(403, 'Anda tidak memiliki hak akses untuk approval ini. (ID Mismatch: Auth '.auth()->id().' vs Appr '.$approval->approver_id.')');
        }

        // Check if user is an HO Approver (Global Approver) or Admin
        $isHO = auth()->user()->hasRole('Admin') || \App\Models\GlobalApproverConfig::where('user_id', auth()->id())->exists();

        // Validate adjusted quantities if provided (only for HO approvers)
        $validated = $request->validate([
            'remarks' => 'nullable|string',
            'adjusted_quantities' => 'nullable|array',
            'adjusted_quantities.*' => 'nullable|numeric|min:0',
        ]);

        $adjustedQuantities = $request->input('adjusted_quantities');
        
        // Ensure empty values are nullifed so they don't override lower levels with treated-as-zero
        if (is_array($adjustedQuantities)) {
            $adjustedQuantities = array_map(function($val) {
                return ($val === '' || $val === null) ? null : $val;
            }, $adjustedQuantities);
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
        \Illuminate\Support\Facades\Log::info('Hold Attempt', [
            'user_id' => auth()->id(),
            'approver_id' => $approval->approver_id,
            'approval_id' => $approval->id,
            'role' => auth()->user()->getRoleNames()
        ]);

        $this->enforceSequentialApproval($approval);

        // Ensure user owns this approval OR is Admin
        if ($approval->approver_id != auth()->id() && !auth()->user()->hasRole('Admin')) {
             abort(403, 'Anda tidak memiliki hak akses untuk approval ini. (ID Mismatch: Auth '.auth()->id().' vs Appr '.$approval->approver_id.')');
        }

        $request->validate(['remarks' => 'required|string']);

        $this->approvalService->hold($approval, $request->input('remarks'));

        return redirect()->route('approval.index')->with('success', 'PR placed On Hold.');
    }

    public function reject(Request $request, PrApproval $approval)
    {
        \Illuminate\Support\Facades\Log::info('Reject Attempt', [
            'user_id' => auth()->id(),
            'approver_id' => $approval->approver_id,
            'approval_id' => $approval->id,
            'role' => auth()->user()->getRoleNames()
        ]);

        $this->enforceSequentialApproval($approval);

        if ($approval->approver_id != auth()->id() && !auth()->user()->hasRole('Admin')) {
             abort(403, 'Anda tidak memiliki hak akses untuk approval ini. (ID Mismatch: Auth '.auth()->id().' vs Appr '.$approval->approver_id.')');
        }
        
        $request->validate(['remarks' => 'required|string']);

        $this->approvalService->reject($approval, $request->input('remarks'));
        
        return redirect()->route('approval.index')->with('success', 'PR Rejected.');
    }

}
