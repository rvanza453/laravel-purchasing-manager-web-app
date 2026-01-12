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
        $query = PurchaseRequest::with(['department', 'subDepartment']);

        // --- Core Visibility Logic (DO NOT CHANGE) ---
        $isHO = $user->hasRole('admin') 
                || ($user->site && $user->site->code === 'HO')
                || \App\Models\GlobalApproverConfig::where('user_id', $user->id)->exists();

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
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('request_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('request_date', '<=', $request->end_date);
        }
        // ------------------------
        
        $prs = $query->orderBy('created_at', 'desc')->paginate(10); // Added pagination for better UI
            
        return view('pr.index', compact('prs'));
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
            'sub_department_id' => 'required|exists:sub_departments,id', // Mandatory now
            'request_date' => 'required|date',
            'description' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    if ($value === 'manual') return; // validation pass
                    if (!empty($value) && !\App\Models\Product::where('id', $value)->exists()) {
                         $fail('Selected product is invalid.');
                    }
                }
            ],
            'items.*.item_name' => 'required|string', 
            'items.*.specification' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string',
            'items.*.price_estimation' => 'required|numeric|min:0',
            'items.*.manual_category' => 'nullable|string',
            'items.*.url_link' => 'nullable|string|url', // Add validation for URL
        ]);
        
        // Custom Validation for Manual Category
        foreach ($request->items as $index => $item) {
             $pid = $item['product_id'] ?? null;
             if (($pid === 'manual' || empty($pid)) && empty($item['manual_category'])) {
                 return back()->withErrors(["items.{$index}.manual_category" => "Category is required for manual items."])->withInput();
             }
        }

        // Process items
        $items = collect($request->items)->map(function($item) {
             if (isset($item['product_id']) && $item['product_id'] === 'manual') {
                 $item['product_id'] = null;
             }
             return $item;
        })->toArray();

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

        // 2. Check Budget Availability
        $year = date('Y', strtotime($request->request_date));
        $subDeptId = $request->sub_department_id;

        foreach ($itemsByCategory as $category => $amountNeeded) {
            $budget = \App\Models\Budget::where('sub_department_id', $subDeptId)
                        ->where('category', $category)
                        ->where('year', $year)
                        ->first();
            
            if (!$budget) {
                if ($category !== 'Uncategorized') {
                     return back()->withInput()->withErrors(['budget' => "No budget configured for category '{$category}' in this Sub Department."]);
                }
                continue; 
            }

            // Calculate current usage (Used in Approved PRs)
            // Note: Should we include Pending PRs? Usually yes for "Reserved" budget.
            // Let's include Pending and Approved. Exclude Rejected.
            $usedAmount = \App\Models\PurchaseRequest::where('sub_department_id', $subDeptId)
                            ->where('status', '!=', 'Rejected')
                            ->whereYear('request_date', $year)
                            ->whereHas('items', function($q) use ($category) {
                                // This is tricky for manual items if we don't store category in pr_items
                                // Doing a loose check based on product relation
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
                // Allow creation but flash warning
                $warnings[] = "Budget Exceeded for '{$category}'. Limit: ".number_format($budget->amount).". Used: ".number_format($usedAmount).". Request: ".number_format($amountNeeded).". Remaining: ".number_format($remaining);
            }
        }
        
        if (!empty($warnings)) {
             session()->flash('warning', implode('<br>', $warnings));
        }

        $this->prService->createPr(
            $request->only('department_id', 'sub_department_id', 'request_date', 'description'), // Add sub_department_id
            $items
        );

        return redirect()->route('pr.index')->with('success', 'PR Submitted successfully.');
    }

    public function show(PurchaseRequest $pr)
    {
        $pr->load('items.product', 'approvals.approver', 'department.site');
        
        // Calculate budget status for this PR
        $year = $pr->request_date->format('Y');
        $subDeptId = $pr->sub_department_id;
        
        // Group items by category (product category or manual)
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

        $budgetWarnings = [];
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
                $budgetWarnings[] = "Budget <strong>{$cat}</strong> akan minus! (Limit: ".number_format($limit).", Terpakai+Request: ".number_format($totalProjected).")";
            }
        }

        return view('pr.show', compact('pr', 'budgetWarnings'));
    }

    public function getBudgetStatus($subDepartmentId)
    {
        $year = date('Y'); // Current year
        $budgets = \App\Models\Budget::where('sub_department_id', $subDepartmentId)
                    ->where('year', $year)
                    ->get()
                    ->keyBy('category');

        $status = [];
        $categories = config('options.product_categories');

        foreach ($categories as $cat) {
            $budgetAmount = $budgets[$cat]->amount ?? 0;
            
            // Calculate usage
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
                                    // Filter items belonging to this category
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

        return response()->json($status);
    }
}
