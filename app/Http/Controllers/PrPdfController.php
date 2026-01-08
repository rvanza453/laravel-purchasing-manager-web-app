<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrPdfController extends Controller
{
    public function export(PurchaseRequest $purchaseRequest)
    {
        // Check if PR is fully approved
        if ($purchaseRequest->status !== 'Approved') {
            abort(403, 'PR belum fully approved. Export PDF hanya tersedia untuk PR yang sudah disetujui semua.');
        }

        // Load relations
        $purchaseRequest->load([
            'user',
            'department.site',
            'subDepartment',
            'items.product',
            'approvals' => function ($query) {
                $query->where('status', 'Approved')
                      ->with('approver')
                      ->orderBy('level');
            }
        ]);

        // Calculate budget information
        $year = $purchaseRequest->request_date->format('Y');
        $subDeptId = $purchaseRequest->sub_department_id;
        
        // Group items by category
        $itemsByCategory = [];
        foreach ($purchaseRequest->items as $item) {
            $cat = 'Lain-lain';
            if ($item->product && $item->product->category) {
                $cat = $item->product->category;
            } elseif ($item->manual_category) {
                $cat = $item->manual_category;
            }
            if (!isset($itemsByCategory[$cat])) $itemsByCategory[$cat] = 0;
            $itemsByCategory[$cat] += $item->subtotal;
        }

        // Calculate budget totals
        $totalBudget = 0;
        $totalActual = 0;
        
        foreach ($itemsByCategory as $cat => $amount) {
            $budget = \App\Models\Budget::where('sub_department_id', $subDeptId)
                        ->where('category', $cat)
                        ->where('year', $year)
                        ->first();
            
            $limit = $budget ? $budget->amount : 0;
            $totalBudget += $limit;
            
            // Calculate actual (other approved PRs, excluding current)
            $otherUsed = \App\Models\PurchaseRequest::where('sub_department_id', $subDeptId)
                            ->where('status', 'Approved')
                            ->where('id', '!=', $purchaseRequest->id)
                            ->whereYear('request_date', $year)
                            ->with(['items' => function($q) use ($cat) {
                                $q->whereHas('product', function($sq) use ($cat) {
                                    $sq->where('category', $cat);
                                })->orWhere('manual_category', $cat);
                            }])
                            ->get()
                            ->sum(function($p) use ($cat) {
                                return $p->items->filter(function($i) use ($cat) {
                                    if ($i->product && $i->product->category === $cat) return true;
                                    if ($i->manual_category === $cat) return true;
                                    return false;
                                })->sum('subtotal');
                            });
                            
            $totalActual += $otherUsed;
        }
        
        $currentRequest = $purchaseRequest->total_estimated_cost;
        $saldo = $totalBudget - ($totalActual + $currentRequest);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.pr_export', [
            'pr' => $purchaseRequest,
            'approvals' => $purchaseRequest->approvals,
            'budgetInfo' => [
                'total' => $totalBudget,
                'actual' => $totalActual,
                'current' => $currentRequest,
                'saldo' => $saldo
            ]
        ]);

        // Set paper size and orientation to landscape for wider table
        $pdf->setPaper('a4', 'landscape');

        // Download PDF with safe filename (replace / with _)
        $safeFilename = str_replace('/', '_', $purchaseRequest->pr_number);
        return $pdf->download("PR_{$safeFilename}.pdf");
    }
}
