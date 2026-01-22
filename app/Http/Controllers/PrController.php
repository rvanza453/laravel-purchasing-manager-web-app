<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Department;
use App\Models\Product;
use App\Services\PrService;
use Illuminate\Http\Request;

class PrController extends Controller
{
    protected $prService;

    public function __construct(PrService $prService)
    {
        $this->prService = $prService;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $query = PurchaseRequest::with(['department', 'subDepartment', 'items']);
        // Eager load relationships
        $query->with(['department', 'subDepartment', 'items.job']);

        // --- Core Visibility Logic (DO NOT CHANGE) ---
        $isHO = $user->hasRole('admin') 
                || ($user->site && $user->site->code === 'HO')
                || \App\Models\GlobalApproverConfig::where('user_id', $user->id)->exists();

        // Prepare Departments for Filter
        if ($isHO) {
            $departments = Department::with('subDepartments')->orderBy('name')->get();
        } else {
            if ($user->department_id) {
                $departments = Department::with('subDepartments')->where('id', $user->department_id)->get();
            } else {
                $departments = collect(); 
            }
        }

        if ($isHO) {
            // HO sees ALL PRs
        } else {
            // Site Staff: View PRs from their Department
            if ($user->department_id) {
                $query->where('department_id', $user->department_id);
            } else {
                $query->where('user_id', $user->id);
            }
        }
        // ---------------------------------------------

        // --- Search & Filters ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pr_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === \App\Enums\PrStatus::PENDING->value) {
                $query->whereIn('status', [\App\Enums\PrStatus::PENDING->value, \App\Enums\PrStatus::ON_HOLD->value]);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('sub_department_id')) {
            $query->where('sub_department_id', $request->sub_department_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('request_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('request_date', '<=', $request->end_date);
        }
        // ------------------------
        
        $prs = $query->orderBy('created_at', 'desc')->paginate(10); // Added pagination for better UI
            
        return view('pr.index', compact('prs', 'departments')); // Pass departments
    }

    public function create()
    {
        // Filter departments: Admin sees all, Staff sees only their own
        if (auth()->user()->hasRole('admin')) {
            $departments = Department::with(['site', 'subDepartments'])->orderBy('name')->get();
        } else {
            // Check if user has a department assigned
            $userDeptId = auth()->user()->department_id;
            if ($userDeptId) {
                // Fetch only the user's department with its sub-departments
                $departments = Department::with(['site', 'subDepartments'])
                                ->where('id', $userDeptId)
                                ->get();
            } else {
                // Fallback if user has no department (shouldn't happen for staff theoretically)
                $departments = collect(); 
            }
        }

        $products = Product::whereNotNull('category')->orderBy('name')->get(); 
        
        $categories = config('options.product_categories');
        return view('pr.create', compact('departments', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'sub_department_id' => 'required|exists:sub_departments,id', 
            'request_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    if ($value === 'manual') return; 
                    if (!empty($value) && !\App\Models\Product::where('id', $value)->exists()) {
                         $fail('Selected product is invalid.');
                    }
                }
            ],
            // New Validation for Job
            'items.*.job_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $dept = Department::find($request->department_id);
                    if ($dept && $dept->budget_type === \App\Enums\BudgetingType::JOB_COA) {
                        if (empty($value)) {
                            $fail('Job / Pekerjaan harus dipilih untuk unit ini.');
                        } elseif (!\App\Models\Job::where('id', $value)->exists()) {
                            $fail('Selected job is invalid.');
                        }
                    }
                }
            ],
            'items.*.item_name' => 'required|string', 
            'items.*.specification' => 'nullable|string',
            'items.*.remarks' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string',
            'items.*.price_estimation' => 'required|numeric|min:0',
            'items.*.manual_category' => 'nullable|string',
            'items.*.url_link' => 'nullable|string|url', 
        ]);
        
        // Custom Validation for Manual Category (Only for Station Budget Type)
        $dept = Department::find($request->department_id);
        if ($dept->budget_type === \App\Enums\BudgetingType::STATION) {
            foreach ($request->items as $index => $item) {
                 $pid = $item['product_id'] ?? null;
                 if (($pid === 'manual' || empty($pid)) && empty($item['manual_category'])) {
                     return back()->withErrors(["items.{$index}.manual_category" => "Category is required for manual items."])->withInput();
                 }
            }
        }

        // Process items
        $items = collect($request->items)->map(function($item) {
             if (isset($item['product_id']) && $item['product_id'] === 'manual') {
                 $item['product_id'] = null;
             }
             return $item;
        })->toArray();

        // Budget Checking Logic
        $year = date('Y', strtotime($request->request_date));
        $subDeptId = $request->sub_department_id;
        $warnings = [];

        if ($dept->budget_type === \App\Enums\BudgetingType::JOB_COA) {
            // Group by Job (Since Job is now the budget unit)
            $itemsByJob = [];
            foreach ($items as $item) {
                if (empty($item['job_id'])) continue;
                $job = \App\Models\Job::find($item['job_id']);
                if (!$job) continue;
                
                $jobId = $job->id;
                // Combine Code and Name for Label
                $label = ($job->code ?? '') . ' - ' . $job->name; 

                if (!isset($itemsByJob[$jobId])) {
                     $itemsByJob[$jobId] = ['amount' => 0, 'name' => $label];
                }
                $itemsByJob[$jobId]['amount'] += ($item['price_estimation'] * $item['quantity']);
            }

            foreach ($itemsByJob as $jobId => $data) {
                $amountNeeded = $data['amount'];
                $budget = \App\Models\Budget::where('sub_department_id', $subDeptId)
                            ->where('job_id', $jobId)
                            ->where('year', $year)
                            ->first();

                if (!$budget) {
                     return back()->withInput()->withErrors(['budget' => "No budget configured for Job '{$data['name']}' in this Sub Department."]);
                }

                // Check Usage (Approved + Pending, exclude Rejected)
                // Filter PRs that have items with this specific Job
                $usedAmount = \App\Models\PurchaseRequest::where('sub_department_id', $subDeptId)
                                ->where('status', '!=', 'Rejected')
                                ->whereYear('request_date', $year)
                                ->with(['items'])
                                ->get()
                                ->sum(function($pr) use ($jobId) {
                                    return $pr->items->filter(function($i) use ($jobId) {
                                        return $i->job_id == $jobId;
                                    })->sum('subtotal');
                                });

                if (($usedAmount + $amountNeeded) > $budget->amount) {
                    $remaining = $budget->amount - $usedAmount;
                    $warnings[] = "Budget Exceeded for Job '{$data['name']}'. Limit: ".number_format($budget->amount).". Used: ".number_format($usedAmount).". Request: ".number_format($amountNeeded).". Remaining: ".number_format($remaining);
                }
            }

        } else {
            // STATION Budget Type (Existing Logic)
            $itemsByCategory = [];
            foreach ($items as $item) {
                $cat = 'Uncategorized';
                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->category) {
                        $cat = $product->category;
                    }
                } elseif (!empty($item['manual_category'])) {
                    $cat = $item['manual_category'];
                } else {
                    $cat = 'Lain-lain'; 
                }

                if (!isset($itemsByCategory[$cat])) {
                    $itemsByCategory[$cat] = 0;
                }
                $itemsByCategory[$cat] += ($item['price_estimation'] * $item['quantity']);
            }

            foreach ($itemsByCategory as $category => $amountNeeded) {
                // Clean category string
                $category = trim($category);

                // Category Mapping (English -> Indo)
                // 'Consumable' in Product Master often corresponds to 'Bahan Pembantu' in Budget Master
                $categoryMap = [
                    'Consumable' => 'Bahan Pembantu',
                ];
                $budgetCategory = $categoryMap[$category] ?? $category;
                
                $budget = \App\Models\Budget::where('sub_department_id', $subDeptId)
                            ->where('category', $budgetCategory)
                            ->where('year', $year)
                            ->where(function($query) {
                                $query->whereNull('job_id')
                                      ->orWhere('job_id', 0);
                            })
                            ->first();
                
                if (!$budget) {
                    if ($category !== 'Uncategorized') {
                         return back()->withInput()->withErrors(['budget' => "No budget configured for category '{$category}' in this Sub Department (Year: {$year}). Please check Master Budget."]);
                    }
                    continue; 
                }

                $usedAmount = \App\Models\PurchaseRequest::where('sub_department_id', $subDeptId)
                                ->where('status', '!=', 'Rejected')
                                ->whereYear('request_date', $year)
                                ->whereHas('items', function($q) use ($category) {
                                    $q->whereHas('product', function($sq) use ($category) {
                                        $sq->where('category', $category);
                                    });
                                })
                                ->with(['items' => function($q) use ($category) {
                                    $q->whereHas('product', function($sq) use ($category) {
                                        $sq->where('category', $category);
                                    });
                                }])
                                ->get()
                                ->sum(function($pr) {
                                    return $pr->items->sum('subtotal');
                                });

                if (($usedAmount + $amountNeeded) > $budget->amount) {
                    $remaining = $budget->amount - $usedAmount;
                    $warnings[] = "Budget Exceeded for '{$category}'. Limit: ".number_format($budget->amount).". Used: ".number_format($usedAmount).". Request: ".number_format($amountNeeded).". Remaining: ".number_format($remaining);
                }
            }
        }
        
        if (!empty($warnings)) {
             session()->flash('warning', implode('<br>', $warnings));
        }

        $prData = $request->only('department_id', 'sub_department_id', 'request_date');
        $prData['description'] = $request->description ?? '-';

        $this->prService->createPr(
            $prData,
            $items
        );

        return redirect()->route('pr.index')->with('success', 'PR Submitted successfully.');
    }

    public function show(PurchaseRequest $pr)
    {
        $pr->load('items.product', 'approvals.approver', 'department.site', 'items.job'); 
        
        // Calculate budget status for this PR
        $year = $pr->request_date->format('Y');
        $subDeptId = $pr->sub_department_id;
        $budgetWarnings = [];

        if ($pr->department->budget_type === \App\Enums\BudgetingType::JOB_COA) {
             // Logic for Job Budget Warning in Show View
             $itemsByJob = [];
             foreach ($pr->items as $item) {
                 if ($item->job) {
                     $jobId = $item->job_id;
                     $key = ($item->job->code ?? '') . ' - ' . $item->job->name;
                     if (!isset($itemsByJob[$jobId])) $itemsByJob[$jobId] = ['amount'=>0, 'name'=>$key];
                     $itemsByJob[$jobId]['amount'] += $item->subtotal;
                 }
             }

             foreach ($itemsByJob as $jobId => $data) {
                // Find Budget
                $budget = \App\Models\Budget::where('sub_department_id', $subDeptId)
                            ->where('job_id', $jobId)
                            ->where('year', $year)
                            ->first();

                 $limit = $budget ? $budget->amount : 0;

                 // Calculate Usage (Other PRs)
                 $otherUsed = \App\Models\PurchaseRequest::where('sub_department_id', $subDeptId)
                                ->where('status', '!=', 'Rejected')
                                ->where('id', '!=', $pr->id)
                                ->whereYear('request_date', $year)
                                ->with(['items'])
                                ->get()
                                ->sum(function($p) use ($jobId) {
                                    return $p->items->filter(function($i) use ($jobId) {
                                        return $i->job_id == $jobId;
                                    })->sum('subtotal');
                                });

                 $totalProjected = $otherUsed + $data['amount'];
                if ($totalProjected > $limit) {
                    $budgetWarnings[] = "Budget <strong>{$data['name']}</strong> akan melebihi limit! (Limit: ".number_format($limit).", Terpakai+Request: ".number_format($totalProjected).")";
                }
             }

        } else {
            // Existing Station Logic
            $itemsByCategory = [];
            foreach ($pr->items as $item) {
                $cat = 'Uncategorized';
                if ($item->product && $item->product->category) {
                    $cat = $item->product->category;
                } elseif ($item->manual_category) {
                    $cat = $item->manual_category;
                } else {
                     $cat = 'Lain-lain';
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
                
                $otherUsed = \App\Models\PurchaseRequest::where('sub_department_id', $subDeptId)
                                ->where('status', '!=', 'Rejected')
                                ->where('id', '!=', $pr->id) // Exclude current
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
                                
                $totalProjected = $otherUsed + $amount;
                
                if ($totalProjected > $limit) {
                    $budgetWarnings[] = "Budget <strong>{$cat}</strong> akan melebihi limit! (Limit: ".number_format($limit).", Terpakai+Request: ".number_format($totalProjected).")";
                }
            }
        }

        return view('pr.show', compact('pr', 'budgetWarnings'));
    }

    public function getBudgetStatus($subDepartmentId)
    {
        $year = date('Y'); // Current year
        $budgets = \App\Models\Budget::where('sub_department_id', $subDepartmentId)
                    ->where('year', $year)
                    ->get();
        
        // We need to know the Dept Budget Type, but here we just have subDeptId.
        $subDept = \App\Models\SubDepartment::find($subDepartmentId);
        if (!$subDept) return response()->json([]);
        $isJobCoa = $subDept->department->budget_type === \App\Enums\BudgetingType::JOB_COA;

        $status = [];
        
        if ($isJobCoa) {
             foreach ($budgets as $budget) {
                 if (!$budget->job) continue;
                 $key = $budget->job_id;
                 $label = ($budget->job->code ?? '') . ' - ' . $budget->job->name;
                 // Usage
                 $usedAmount = \App\Models\PurchaseRequest::where('sub_department_id', $subDepartmentId)
                                ->where('status', '!=', 'Rejected')
                                ->whereYear('request_date', $year)
                                ->with(['items'])
                                ->get()
                                ->sum(function($pr) use ($key) {
                                    return $pr->items->filter(function($i) use ($key) {
                                        return $i->job_id == $key;
                                    })->sum('subtotal');
                                });
                 
                 $status[$label] = [ // Use Label as Key specifically for text display if needed, or ID
                    'limit' => $budget->amount,
                    'used' => $usedAmount,
                    'remaining' => $budget->amount - $usedAmount
                 ];
             }
        } else {
            $categories = config('options.product_categories');
            $budgetsByKey = $budgets->keyBy('category');
            
            foreach ($categories as $cat) {
                $budgetAmount = $budgetsByKey[$cat]->amount ?? 0;
                $usedAmount = \App\Models\PurchaseRequest::where('sub_department_id', $subDepartmentId)
                                ->where('status', '!=', 'Rejected')
                                ->whereYear('request_date', $year)
                                ->with(['items' => function($q) use ($cat) {
                                    $q->whereHas('product', function($sq) use ($cat) {
                                        $sq->where('category', $cat);
                                    })->orWhere('manual_category', $cat);
                                }])
                                ->get()
                                ->sum(function($pr) use ($cat) {
                                    return $pr->items->filter(function($item) use ($cat) {
                                        if ($item->product && $item->product->category === $cat) return true;
                                        if ($item->manual_category === $cat) return true;
                                        return false;
                                    })->sum('subtotal');
                                });
    
                $status[$cat] = [
                    'limit' => $budgetAmount,
                    'used' => $usedAmount,
                    'remaining' => $budgetAmount - $usedAmount
                ];
            }
        }

        return response()->json($status);
    }
    
    // New Endpoint for Jobs
    public function getJobs(\App\Models\SubDepartment $subDepartment)
    {
        // Return All Global Jobs
        $jobs = \App\Models\Job::orderBy('code')
                ->get()
                ->map(function($job) {
                    return [
                        'id' => $job->id,
                        'name' => $job->name,
                        'code' => $job->code,
                        'label' => ($job->code ? $job->code . ' - ' : '') . $job->name
                    ];
                });
        return response()->json($jobs);
    }
}
