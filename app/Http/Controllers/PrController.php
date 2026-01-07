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

    public function index()
    {
        // Admin can see all PRs, regular users see only their own
        $query = PurchaseRequest::with('department');
        
        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }
        
        $prs = $query->orderBy('created_at', 'desc')->get();
            
        return view('pr.index', compact('prs'));
    }

    public function create()
    {
        $departments = Department::with('site')->get();
        // Maybe filter by user's authorized department? For now show all.
        $products = Product::all(); // Master Products
        return view('pr.create', compact('departments', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'request_date' => 'required|date',
            'description' => 'required|string',
            'items' => 'required|array|min:1',
            // 'items.*.product_id' => 'nullable|exists:products,id', // WE CANNOT enforce exists if 'manual' is sent as string.
            // Instead, we validate that IF it's an integer, it exists. IF "manual", it's fine.
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
        ]);

        $items = collect($request->items)->map(function($item) {
             if (isset($item['product_id']) && $item['product_id'] === 'manual') {
                 $item['product_id'] = null;
             }
             return $item;
        })->toArray();

        $this->prService->createPr($request->only('department_id', 'request_date', 'description'), $items);

        return redirect()->route('pr.index')->with('success', 'PR Submitted successfully.');
    }

    public function show(PurchaseRequest $pr)
    {
        $pr->load('items.product', 'approvals.approver', 'department.site');
        return view('pr.show', compact('pr'));
    }
}
