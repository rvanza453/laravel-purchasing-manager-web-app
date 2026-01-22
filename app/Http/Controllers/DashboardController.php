<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Enums\PrStatus;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isHO = false;
        
        // Check if HO User (by Site Code 'HO' or GlobalApproverConfig)
        if ($user->site && $user->site->code === 'HO') {
            $isHO = true;
        } elseif (\App\Models\GlobalApproverConfig::where('user_id', $user->id)->exists()) {
            $isHO = true;
        } elseif ($user->hasRole('admin')) {
            $isHO = true;
        }

        $isApprover = $user->hasRole('Approver');

        // 1. Stats Query
        $statsQuery = PurchaseRequest::query();

        if (!$isHO) {
            if ($isApprover) {
                // Approver: Show all PRs from their site
                $statsQuery->whereHas('user', function($q) use ($user) {
                    $q->where('site_id', $user->site_id);
                });
            } else {
                // Regular User: Show only their own PRs
                $statsQuery->where('user_id', $user->id);
            }
        }
        // HO sees all, no filter needed

        $stats = [
            'pending_approval' => (clone $statsQuery)->whereIn('status', [PrStatus::PENDING->value, PrStatus::ON_HOLD->value])->count(),
            'approved' => (clone $statsQuery)->where('status', PrStatus::APPROVED->value)->count(),
            'rejected' => (clone $statsQuery)->where('status', PrStatus::REJECTED->value)->count(),
            'po_created' => (clone $statsQuery)->where('status', PrStatus::PO_CREATED->value)->count(),
        ];

        // 2. Budget Chart (Budget used per department)
        $chartQuery = PurchaseRequest::join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('SUM(total_estimated_cost) as total'))
            ->groupBy('departments.name');

        if (!$isHO) {
            // Both Approver and Regular User see charts only for their site's departments
            $chartQuery->where('departments.site_id', $user->site_id);
        }

        $budgetChart = $chartQuery->get();
            
        // 3. Budget Summary Calculation
        $currentYear = date('Y');
        $deptQuery = \App\Models\Department::with(['subDepartments.budgets' => function($q) use ($currentYear, $isHO) {
             $q->where('year', $currentYear);
        }]);

        if (!$isHO) {
            // Filter departments by user's site
            $deptQuery->where('site_id', $user->site_id);
        }

        $departments = $deptQuery->get();

        $departmentBudgets = $departments->map(function ($dept) use ($currentYear, $isHO) {
            // Filter budgets by type just in case (though we did cleanup)
            $validBudgets = $dept->subDepartments->flatMap(function($sub) use ($dept) {
                return $sub->budgets->filter(function($b) use ($dept) {
                    if ($dept->budget_type === \App\Enums\BudgetingType::JOB_COA) {
                        return !is_null($b->job_id);
                    }
                    return !is_null($b->category);
                });
            });

            // Calculate Allocated
            $allocated = $validBudgets->sum('amount');

            // Calculate Used Budget (Sum of Approved/PO PRs)
            $used = \App\Models\PurchaseRequest::where('department_id', $dept->id)
                ->whereIn('status', [PrStatus::APPROVED->value, PrStatus::PO_CREATED->value])
                ->whereYear('request_date', $currentYear)
                ->sum('total_estimated_cost');

            $remaining = $allocated - $used;

            $dept->calculated_budget = $allocated;
            $dept->used_budget = $used;
            $dept->remaining_budget = $remaining;
            
            return $dept;
        });

        return view('dashboard', compact('stats', 'budgetChart', 'departmentBudgets'));
    }
}
