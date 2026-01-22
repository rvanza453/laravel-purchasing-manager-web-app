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

        $purchaseRequest->load([
            'user',
            'department.site',
            'subDepartment',
            'items.product',
            'items.job',
            'approvals' => function ($query) {
                $query->where('status', 'Approved')
                      ->with('approver')
                      ->orderBy('level');
            }
        ]);
        
        $year = $purchaseRequest->request_date->format('Y');
        $subDeptId = $purchaseRequest->sub_department_id;
        $dept = $purchaseRequest->department;

        $isJobCoa = $dept->budget_type === \App\Enums\BudgetingType::JOB_COA;
        $subDept = $purchaseRequest->subDepartment;
        $subDeptName = $subDept ? ($subDept->coa ? $subDept->coa . ' - ' : '') . $subDept->name : '-';
        
        $jobName = ($dept->name ?? '-') . ' / ' . $subDeptName;
        
        $totalBudget = 0;
        $totalActual = 0;

        if ($isJobCoa) {
            // Logic for Job Based PR
            // Assuming single job per PR as enforced in Create
            $firstItem = $purchaseRequest->items->first();
            $jobId = $firstItem ? $firstItem->job_id : null;
            
            if ($jobId) {
                $job = \App\Models\Job::find($jobId);
                if ($job) {
                    $jobName .= ' / ' . ($job->code ? $job->code . ' - ' : '') . $job->name;
                    
                    // Specific Job Budget
                    $budget = \App\Models\Budget::where('sub_department_id', $subDeptId)
                                ->where('job_id', $jobId)
                                ->where('year', $year)
                                ->first();
                    
                    $totalBudget = $budget ? $budget->amount : 0;
                    
                    // Calculate actual usage for this Job from other Approved PRs
                    $totalActual = \App\Models\PurchaseRequest::where('sub_department_id', $subDeptId)
                                    ->where('status', 'Approved')
                                    ->where('id', '!=', $purchaseRequest->id)
                                    ->whereYear('request_date', $year)
                                    ->with(['items'])
                                    ->get()
                                    ->sum(function($pr) use ($jobId) {
                                        return $pr->items->filter(function($i) use ($jobId) {
                                            return $i->job_id == $jobId;
                                        })->sum('subtotal');
                                    });
                }
            }
        } else {
            // Logic for Station Based PR (Category Grouping)
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
        }
        
        // Calculate current request based on Approved Quantities (Final)
        $currentRequest = $purchaseRequest->items->sum(function($item) {
            return $item->getFinalQuantity() * $item->price_estimation;
        });
        
        $saldo = $totalBudget - ($totalActual + $currentRequest);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.pr_export', [
            'pr' => $purchaseRequest,
            'approvals' => $purchaseRequest->approvals,
            'jobName' => $jobName,
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
