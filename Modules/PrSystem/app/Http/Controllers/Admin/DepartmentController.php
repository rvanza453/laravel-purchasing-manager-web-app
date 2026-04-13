<?php

namespace Modules\PrSystem\Http\Controllers\Admin;

use App\Models\User;
use Modules\PrSystem\Http\Controllers\Controller;
use Modules\PrSystem\Models\Department;
use Modules\PrSystem\Models\Site;
use Modules\PrSystem\Models\ApproverConfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['site', 'approverConfigs.user'])
                        ->orderBy('name')
                        ->get()
                        ->groupBy(function($dept) {
                            return $dept->site->name ?? 'No Site';
                        });
        $globalApprovers = \Modules\PrSystem\Models\GlobalApproverConfig::with('user')->orderBy('level')->get();
        return view('prsystem::admin.departments.index', compact('departments', 'globalApprovers'));
    }

    public function create()
    {
        return redirect()->route('master-departments.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('master-departments.index');
    }

    public function edit(Department $department)
    {
        $sites = Site::all();

        // PR approver candidates: prefer module role assignment, keep legacy Spatie role as fallback.
        $users = User::where('site_id', $department->site_id)
            ->where(function ($q) {
                $q->whereHas('moduleRoles', function ($mr) {
                    $mr->where('module_key', 'pr')
                       ->where('role_name', 'Approver');
                })->orWhereHas('roles', function ($r) {
                    $r->where('name', 'Approver');
                });
            })
            ->orderBy('name')
            ->get();
        
        // No need to load subDepartments here anymore

        return view('prsystem::admin.departments.edit', compact('department', 'sites', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'approvers' => 'array',
            'approvers.*.user_id' => 'required|exists:users,id',
            'approvers.*.role_name' => 'required|string',
            'approvers.*.level' => 'required|integer',
            'use_global_approval' => 'boolean',
            'budget_type' => 'required|in:station,job_coa',
        ]);

        $department->update([
            'use_global_approval' => $request->has('use_global_approval'),
            'budget_type' => $request->budget_type,
        ]);

        // Sync Approvers
        $department->approverConfigs()->delete();
        if ($request->has('approvers')) {
            foreach ($request->approvers as $approver) {
                $department->approverConfigs()->create([
                    'user_id' => $approver['user_id'],
                    'role_name' => $approver['role_name'],
                    'level' => $approver['level'],
                ]);
            }
        }

        \Modules\PrSystem\Helpers\ActivityLogger::log('updated', 'Updated Department Configuration: ' . $department->name, $department);

        return redirect()->route('departments.index')->with('success', 'Approval configuration updated successfully.');
    }

    public function destroy(Department $department)
    {
        return redirect()->route('master-departments.index')->with('error', 'Please use Department Management to delete departments.');
    }

    public function getDepartmentsBySite(Site $site)
    {
        return response()->json($site->departments()->select('id', 'name')->orderBy('name')->get());
    }
}
