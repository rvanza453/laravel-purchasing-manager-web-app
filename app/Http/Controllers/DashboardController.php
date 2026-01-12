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
        // Fetch Stats
        $stats = [
            'pending_approval' => PurchaseRequest::where('status', PrStatus::PENDING->value)->count(),
            'approved' => PurchaseRequest::where('status', PrStatus::APPROVED->value)->count(),
            'rejected' => PurchaseRequest::where('status', PrStatus::REJECTED->value)->count(),
            'po_created' => PurchaseRequest::where('status', PrStatus::PO_CREATED->value)->count(),
        ];

        // Chart Data (Budget used per department)
        $budgetChart = PurchaseRequest::join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('SUM(total_estimated_cost) as total'))
            ->groupBy('departments.name')
            ->get();
            
        // Budget Summary Calculation
        $currentYear = date('Y');
        $departments = \App\Models\Department::with(['subDepartments.budgets' => function($q) use ($currentYear) {
            $q->where('year', $currentYear);
        }])->get();

        $departmentBudgets = $departments->map(function ($dept) use ($currentYear) {
            // Calculate Allocated Budget (Sum of Sub-Dept Budgets)
            $allocated = $dept->subDepartments->sum(function ($sub) {
                return $sub->budgets->sum('amount');
            });

            // Calculate Used Budget (Sum of Approved/PO PRs)
            $used = \App\Models\PurchaseRequest::where('department_id', $dept->id)
                ->whereIn('status', [PrStatus::APPROVED->value, PrStatus::PO_CREATED->value])
                ->whereYear('request_date', $currentYear)
                ->sum('total_estimated_cost');

            $remaining = $allocated - $used;

            // Return custom object or merge into dept
            $dept->calculated_budget = $allocated;
            $dept->used_budget = $used;
            $dept->remaining_budget = $remaining;
            
            return $dept;
        });

        return view('dashboard', compact('stats', 'budgetChart', 'departmentBudgets'));
    }
}
