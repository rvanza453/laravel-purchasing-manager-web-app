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

        return view('dashboard', compact('stats', 'budgetChart'));
    }
}
