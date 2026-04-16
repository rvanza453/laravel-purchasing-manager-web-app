<?php

namespace Modules\PrSystem\Http\Controllers;

use Modules\PrSystem\Models\PrApproval;
use Modules\PrSystem\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $approvalService;
    protected $fonnteService;

    public function __construct(ApprovalService $approvalService, \Modules\PrSystem\Services\FonnteService $fonnteService)
    {
        $this->approvalService = $approvalService;
        $this->fonnteService = $fonnteService;
    }

    private function userHasPrRole(string|array $roles): bool
    {
        $user = auth()->user();

        return $user->hasModuleRole('pr', $roles) || $user->hasRole($roles);
    }

    private function isPrAdmin(): bool
    {
        return $this->userHasPrRole('Admin');
    }

    private function isStrictPrAdmin(): bool
    {
        return auth()->user()?->moduleRole('pr') === 'Admin';
    }

    private function canCurrentUserHandleCapexStep(
        \Modules\PrSystem\Models\CapexApproval $approval,
        \Modules\PrSystem\Models\CapexColumnConfig $config
    ): bool {
        $user = auth()->user();

        if ($this->isPrAdmin()) {
            return true;
        }

        if ($config->approver_user_id && (int) $config->approver_user_id === (int) $user->id) {
            return true;
        }

        return filled($config->approver_role)
            && ($user->hasModuleRole('pr', $config->approver_role) || $user->hasRole($config->approver_role));
    }

    public function index()
    {
        // 1. Get PR Approvals
        $query = PrApproval::whereIn('status', ['Pending', 'On Hold'])
            ->with(['purchaseRequest.user', 'purchaseRequest.department', 'purchaseRequest.items', 'purchaseRequest.approvals']);
        
        if (!$this->isPrAdmin()) {
            $query->where('approver_id', auth()->id());
        }
        
        $prApprovals = $query->orderBy('created_at', 'asc')->get();
        
        $filteredPr = $prApprovals->filter(function ($approval) {

            if (!$approval->purchaseRequest) return false;

            $allPreviousApproved = $approval->purchaseRequest->approvals
                ->filter(function ($other) use ($approval) {
                    return $other->level < $approval->level;
                })
                ->every(function ($other) {
                    return $other->status === \Modules\PrSystem\Enums\PrStatus::APPROVED->value;
                });

            return $allPreviousApproved && in_array($approval->status, ['Pending', 'On Hold']);
        });

        // 2. Get CAPEX approvals pending for current user/admin
        $allCapexApprovals = \Modules\PrSystem\Models\CapexApproval::whereIn('status', ['Pending', 'On Hold'])
            ->with(['capexRequest.user', 'capexRequest.department', 'capexRequest.capexBudget.capexAsset'])
            ->orderBy('created_at', 'asc')
            ->get();

        $filteredCapex = $allCapexApprovals->filter(function ($approval) {
            if (!$approval->capexRequest) {
                return false;
            }

            // Only show active step in inbox
            if ((int) $approval->capexRequest->current_step !== (int) $approval->column_index) {
                return false;
            }

            $config = \Modules\PrSystem\Models\CapexColumnConfig::where('department_id', $approval->capexRequest->department_id)
                ->where('column_index', $approval->column_index)
                ->first();

            if (!$config) {
                return $this->isPrAdmin();
            }

            // Wet signature step remains admin-only.
            if (!$config->is_digital) {
                return $this->isPrAdmin();
            }

            return $this->canCurrentUserHandleCapexStep($approval, $config);
        });

        return view('prsystem::approval.index', [
            'approvals' => $filteredPr,
            'capexApprovals' => $filteredCapex
        ]);
    }

    private function enforceSequentialApproval(PrApproval $approval)
    {
        // Allow Admin to bypass sequential enforcement
        if ($this->isPrAdmin()) {
            return;
        }
        
        // Cek apakah ada level di bawahnya yang belum Approved (Status bukan Approved)
        $hasPendingLowerLevel = PrApproval::where('purchase_request_id', $approval->purchase_request_id)
            ->where('level', '<', $approval->level)
            ->whereNotIn('status', [\Modules\PrSystem\Enums\PrStatus::APPROVED->value]) 
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
           if ($approval->approver_id != auth()->id() && !$this->isPrAdmin()) {
             abort(403, 'Anda tidak memiliki hak akses untuk approval ini. (ID Mismatch: Auth '.auth()->id().' vs Appr '.$approval->approver_id.')');
        }

        // Check if user is an HO Approver (Global Approver) or Admin
        $isHO = $this->isPrAdmin() || \Modules\PrSystem\Models\GlobalApproverConfig::where('user_id', auth()->id())->exists();

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
           if ($approval->approver_id != auth()->id() && !$this->isPrAdmin()) {
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

           if ($approval->approver_id != auth()->id() && !$this->isPrAdmin()) {
             abort(403, 'Anda tidak memiliki hak akses untuk approval ini. (ID Mismatch: Auth '.auth()->id().' vs Appr '.$approval->approver_id.')');
        }
        
        $request->validate(['remarks' => 'required|string']);

        $this->approvalService->reject($approval, $request->input('remarks'));
        
        return redirect()->route('approval.index')->with('success', 'PR Rejected.');
    }

    public function revert(Request $request, PrApproval $approval)
    {
        \Illuminate\Support\Facades\Log::info('Revert Attempt', [
            'user_id' => auth()->id(),
            'approval_id' => $approval->id,
            'role' => auth()->user()->getRoleNames()
        ]);

        // Only Admin can revert approvals
        if (!$this->isStrictPrAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk fitur Batal Approve. (Khusus Admin)');
        }

        try {
            $this->approvalService->revert($approval);
            return back()->with('success', 'Aksi Approval berhasil dibatalkan (Undo). PR kembali ke antrian Pending.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Undo: ' . $e->getMessage());
        }
    }
}
