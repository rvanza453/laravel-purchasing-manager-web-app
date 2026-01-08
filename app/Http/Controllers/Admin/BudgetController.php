<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\SubDepartment;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $site_id = $request->site_id;
        $department_id = $request->department_id;
        $year = date('Y');

        if ($department_id) {
            $department = \App\Models\Department::with('site')->findOrFail($department_id);
            $subDepartments = SubDepartment::where('department_id', $department_id)
                ->with(['budgets' => function($q) use ($year) {
                    $q->where('year', $year);
                }])
                ->get();
            return view('admin.budget.index', compact('subDepartments', 'department', 'year'));
        }

        if ($site_id) {
            $site = \App\Models\Site::findOrFail($site_id);
            $departments = \App\Models\Department::where('site_id', $site_id)
                ->with(['subDepartments.budgets' => function($q) use ($year) {
                    $q->where('year', $year);
                }])
                ->get();
            
            // Calculate total budget per department
            $departments->each(function($dept) use ($year) {
                $dept->total_budget = $dept->subDepartments->flatMap->budgets->sum('amount');
            });

            return view('admin.budget.index', compact('departments', 'site', 'year'));
        }

        $sites = \App\Models\Site::with(['departments.subDepartments.budgets' => function($q) use ($year) {
                $q->where('year', $year);
            }])
            ->get();

        $sites->each(function($site) {
            $site->total_budget = $site->departments->flatMap->subDepartments->flatMap->budgets->sum('amount');
            $site->dept_count = $site->departments->count();
        });

        return view('admin.budget.index', compact('sites', 'year'));
    }

    public function edit(SubDepartment $subDepartment)
    {
        $subDepartment->load('budgets');
        $categories = config('options.product_categories');
        
        // Map existing budgets for easy access in view
        $existingBudgets = $subDepartment->budgets->pluck('amount', 'category')->toArray();

        return view('admin.budget.edit', compact('subDepartment', 'categories', 'existingBudgets'));
    }

    public function update(Request $request, SubDepartment $subDepartment)
    {
        $request->validate([
            'budgets' => 'array',
            'budgets.*' => 'nullable|numeric|min:0',
        ]);

        $year = date('Y');

        foreach ($request->budgets as $category => $amount) {
            Budget::updateOrCreate(
                [
                    'sub_department_id' => $subDepartment->id,
                    'category' => $category,
                    'year' => $year
                ],
                [
                    'amount' => $amount ?? 0
                ]
            );
        }

        return redirect()->route('admin.budgets.index')
            ->with('success', 'Budget updated successfully.');
    }
}
