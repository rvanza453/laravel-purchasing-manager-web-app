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
        // Admin can see all pending approvals, regular users see only their assigned approvals
        $query = PrApproval::where('status', 'Pending')
            ->with(['purchaseRequest.user', 'purchaseRequest.department', 'purchaseRequest.items']);
        
        if (!auth()->user()->hasRole('admin')) {
            $query->where('approver_id', auth()->id());
        }
        
        $approvals = $query->orderBy('created_at', 'asc')->get();

        return view('approval.index', compact('approvals'));
    }

    public function approve(Request $request, PrApproval $approval)
    {
        // Ensure user owns this approval OR is Admin
        if ($approval->approver_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Validate adjusted quantities if provided (optional for HO approvers)
        $validated = $request->validate([
            'remarks' => 'nullable|string',
            'adjusted_quantities' => 'nullable|array',
            'adjusted_quantities.*' => 'nullable|numeric|min:0',
        ]);

        $this->approvalService->approve(
            $approval, 
            $request->input('remarks'),
            $request->input('adjusted_quantities')
        );

        return redirect()->route('approval.index')->with('success', 'PR Approved successfully.');
    }

    public function reject(Request $request, PrApproval $approval)
    {
        if ($approval->approver_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        
        $request->validate(['remarks' => 'required|string']);

        $this->approvalService->reject($approval, $request->input('remarks'));

        return redirect()->route('approval.index')->with('success', 'PR Rejected.');
    }
}
