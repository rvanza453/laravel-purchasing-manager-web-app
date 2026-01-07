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
        // Show tasks assigned to current user
        // Filter approvals where approver_id = auth->id AND status = Pending
        $approvals = PrApproval::where('approver_id', auth()->id())
            ->where('status', 'Pending')
            ->with(['purchaseRequest.user', 'purchaseRequest.category'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('approval.index', compact('approvals'));
    }

    public function approve(Request $request, PrApproval $approval)
    {
        // Ensure user owns this approval
        if ($approval->approver_id !== auth()->id()) {
            abort(403);
        }

        $this->approvalService->approve($approval, $request->input('remarks'));

        return redirect()->route('approval.index')->with('success', 'PR Approved successfully.');
    }

    public function reject(Request $request, PrApproval $approval)
    {
        if ($approval->approver_id !== auth()->id()) {
            abort(403);
        }
        
        $request->validate(['remarks' => 'required|string']);

        $this->approvalService->reject($approval, $request->input('remarks'));

        return redirect()->route('approval.index')->with('success', 'PR Rejected.');
    }
}
