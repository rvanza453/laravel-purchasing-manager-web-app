<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CapexBudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = \App\Models\CapexBudget::with(['department', 'capexAsset'])->latest()->get();
        $departments = \App\Models\Department::all();
        $assets = \App\Models\CapexAsset::active()->get();
        
        return view('admin.capex.budgets.index', compact('budgets', 'departments', 'assets'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'capex_asset_id' => 'required|exists:capex_assets,id',
            // 'budget_code' => 'auto-generated',
            'amount' => 'required|numeric|min:0',
            'original_quantity' => 'required|integer|min:1',
            'is_budgeted' => 'boolean',
            'fiscal_year' => 'required|integer|min:2020|max:2099',
        ]);

        $validated['remaining_amount'] = $validated['amount'];
        $validated['remaining_quantity'] = $validated['original_quantity'];
        $validated['is_budgeted'] = $request->has('is_budgeted');
        
        \App\Models\CapexBudget::create($validated);

        return redirect()->route('admin.capex.budgets.index')->with('success', 'Capex Budget Created Successfully');
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\CapexBudget $budget)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);
        
        // Adjust remaining if total amount changes (simplified logic)
        $diff = $validated['amount'] - $budget->amount;
        $budget->remaining_amount += $diff;
        $budget->amount = $validated['amount'];
        $budget->is_active = $validated['is_active'] ?? $budget->is_active;
        $budget->save();

        return redirect()->route('admin.capex.budgets.index')->with('success', 'Capex Budget Updated Successfully');
    }

    public function destroy(\App\Models\CapexBudget $budget)
    {
        $budget->delete();
        return redirect()->route('admin.capex.budgets.index')->with('success', 'Capex Budget Deleted Successfully');
    }

    public function addPta(Request $request, \App\Models\CapexBudget $budget)
    {
        $validated = $request->validate([
            'pta_amount' => 'required|numeric|min:1',
        ]);

        $budget->pta_amount += $validated['pta_amount'];
        $budget->remaining_amount += $validated['pta_amount'];
        $budget->save();

        return redirect()->route('admin.capex.budgets.index')->with('success', 'PTA (Tambahan Anggaran) berhasil ditambahkan.');
    }
}
