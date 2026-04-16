<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\ServiceAgreementSystem\Models\Department;
use Modules\ServiceAgreementSystem\Models\Job;
use Modules\ServiceAgreementSystem\Models\SubDepartment;
use Modules\ServiceAgreementSystem\Models\UspkBudgetActivity;

class UspkBudgetController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->query('year', now()->year);
        $selectedDepartment = $request->query('department_id');

        $departments = Department::with('site')->orderBy('name')->get(['id', 'site_id', 'name']);
        $jobs = Job::orderBy('name')->get(['id', 'site_id', 'code', 'name']);

        $budgetsQuery = UspkBudgetActivity::query()
            ->with(['subDepartment.department', 'job'])
            ->where('year', $selectedYear)
            ->orderBy('sub_department_id')
            ->orderBy('job_id');

        if (!empty($selectedDepartment)) {
            $budgetsQuery->whereHas('subDepartment', function ($query) use ($selectedDepartment) {
                $query->where('department_id', $selectedDepartment);
            });
        }

        $budgets = $budgetsQuery->get();

        $subDepartments = SubDepartment::query()
            ->with('department:id,name')
            ->orderBy('name')
            ->get(['id', 'department_id', 'name']);

        return view('serviceagreementsystem::uspk-budget.index', [
            'selectedYear' => $selectedYear,
            'selectedDepartment' => $selectedDepartment,
            'departments' => $departments,
            'subDepartments' => $subDepartments,
            'jobs' => $jobs,
            'budgets' => $budgets,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'department_id' => ['required', 'exists:departments,id'],
            'sub_department_id' => ['required', 'exists:sub_departments,id'],
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.job_id' => ['required', 'integer', 'distinct', 'exists:jobs,id'],
            'rows.*.budget_amount' => ['required', 'numeric', 'min:0'],
            'rows.*.description' => ['nullable', 'string'],
        ], [
            'rows.*.job_id.distinct' => 'Job tidak boleh duplikat dalam satu penyimpanan budget.',
        ]);

        $subDepartment = SubDepartment::findOrFail($validated['sub_department_id']);
        if ((int) $subDepartment->department_id !== (int) $validated['department_id']) {
            return back()->withInput()->with('error', 'Afdeling tidak sesuai dengan Site/Department yang dipilih.');
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['rows'] as $row) {
                UspkBudgetActivity::updateOrCreate(
                    [
                        'sub_department_id' => $validated['sub_department_id'],
                        'job_id' => $row['job_id'],
                        'year' => $validated['year'],
                    ],
                    [
                        'budget_amount' => $row['budget_amount'],
                        'description' => $row['description'] ?? null,
                        'is_active' => true,
                    ]
                );
            }
        });

        return redirect()
            ->route('sas.uspk-budgets.index', [
                'year' => $validated['year'],
                'department_id' => $validated['department_id'],
            ])
            ->with('success', 'Budget USPK berhasil disimpan untuk tahun ' . $validated['year'] . '.');
    }
}
