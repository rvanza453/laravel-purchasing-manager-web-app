<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $departments = \App\Models\Department::with('site')->orderBy('name')->get();
        return view('admin.budget.index', compact('departments'));
    }

    public function update(Request $request, \App\Models\Department $department)
    {
        $request->validate(['budget' => 'required|numeric']);
        
        $department->update(['budget' => $request->budget]);

        return back()->with('success', 'Budget updated successfully.');
    }
}
