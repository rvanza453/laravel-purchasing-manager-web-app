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
                ->with(['budgets' => function($q) use ($year, $department) {
                    $q->where('year', $year);
                    // Filter by budget type
                    if ($department->budget_type === \App\Enums\BudgetingType::JOB_COA) {
                        $q->whereNotNull('job_id');
                    } else {
                        $q->whereNotNull('category');
                    }
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
        $subDepartment->load(['budgets', 'department']);
        $isJobCoa = $subDepartment->department->budget_type === \App\Enums\BudgetingType::JOB_COA;
        
        $year = date('Y');
        
        // Clean up incompatible budgets (e.g., category budgets when type is job_coa)
        if ($isJobCoa) {
            // Delete category-based budgets for this sub-department
            Budget::where('sub_department_id', $subDepartment->id)
                ->where('year', $year)
                ->whereNotNull('category')
                ->delete();
        } else {
            // Delete job-based budgets for this sub-department
            Budget::where('sub_department_id', $subDepartment->id)
                ->where('year', $year)
                ->whereNotNull('job_id')
                ->delete();
        }
        
        // Reload budgets after cleanup
        $subDepartment->load('budgets');
        
        $categories = config('options.product_categories');
        $jobs = [];

        if ($isJobCoa) {
            // Fetch all Global Jobs
            $jobs = \App\Models\Job::orderBy('code')->get();
            // Map existing budgets by job_id
            $existingBudgets = $subDepartment->budgets->pluck('amount', 'job_id')->toArray();
        } else {
            // Map existing budgets by category
            $existingBudgets = $subDepartment->budgets->pluck('amount', 'category')->toArray();
        }

        return view('admin.budget.edit', compact('subDepartment', 'categories', 'existingBudgets', 'isJobCoa', 'jobs'));
    }

    public function update(Request $request, SubDepartment $subDepartment)
    {
        $request->validate([
            'budgets' => 'array',
            'budgets.*' => 'nullable|numeric|min:0',
        ]);

        $year = date('Y');
        $subDepartment->load('department');
        $isJobCoa = $subDepartment->department->budget_type === \App\Enums\BudgetingType::JOB_COA;

        if ($isJobCoa) {
            // Key is Job ID
            // First, delete all existing job budgets for this sub-department and year
            Budget::where('sub_department_id', $subDepartment->id)
                ->where('year', $year)
                ->whereNotNull('job_id')
                ->delete();
            
            // Then create new budgets only for jobs with non-zero amounts
            foreach ($request->budgets as $jobId => $amount) {
                if ($amount > 0) {
                    Budget::create([
                        'sub_department_id' => $subDepartment->id,
                        'job_id' => $jobId,
                        'year' => $year,
                        'amount' => $amount,
                        'category' => null
                    ]);
                }
            }
        } else {
            // Key is Category Name
            // First, delete all existing category budgets for this sub-department and year
            Budget::where('sub_department_id', $subDepartment->id)
                ->where('year', $year)
                ->whereNotNull('category')
                ->delete();
            
            // Then create new budgets only for categories with non-zero amounts
            foreach ($request->budgets as $category => $amount) {
                if ($amount > 0) {
                    Budget::create([
                        'sub_department_id' => $subDepartment->id,
                        'category' => $category,
                        'year' => $year,
                        'amount' => $amount,
                        'job_id' => null
                    ]);
                }
            }
        }

        return redirect()->route('admin.budgets.index')
            ->with('success', 'Budget updated successfully.');
    }
}
