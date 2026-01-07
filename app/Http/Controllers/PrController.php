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
        $prs = PurchaseRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
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
            'items.*.product_id' => 'nullable|exists:products,id', // Can be null if adhoc? Maybe enforce master data for now? Let's allow nullable but encourage product_id
            'items.*.item_name' => 'required|string', // If product_id, this is auto-filled or override
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string',
            'items.*.price_estimation' => 'required|numeric|min:0',
        ]);

        $this->prService->createPr($request->only('department_id', 'request_date', 'description'), $request->items);

        return redirect()->route('pr.index')->with('success', 'PR Submitted successfully.');
    }

    public function show(PurchaseRequest $pr)
    {
        $pr->load('items.product', 'approvals.approver', 'department.site');
        return view('pr.show', compact('pr'));
    }
}
