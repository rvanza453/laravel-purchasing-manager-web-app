<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Site;
use App\Models\User;
use App\Models\ApproverConfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['site', 'approverConfigs.user'])->get();
        $globalApprovers = \App\Models\GlobalApproverConfig::with('user')->orderBy('level')->get();
        return view('admin.departments.index', compact('departments', 'globalApprovers'));
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
        $users = User::where('department_id', $department->id)->orWhere('site_id', $department->site_id)->get(); 
        if ($users->isEmpty()) {
             $users = User::all(); 
        }
        
        // No need to load subDepartments here anymore

        return view('admin.departments.edit', compact('department', 'sites', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'approvers' => 'array',
            'approvers.*.user_id' => 'required|exists:users,id',
            'approvers.*.role_name' => 'required|string',
            'approvers.*.level' => 'required|integer',
            'use_global_approval' => 'boolean',
        ]);

        $department->update([
            'use_global_approval' => $request->has('use_global_approval')
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

        return redirect()->route('departments.index')->with('success', 'Approval configuration updated successfully.');
    }

    public function destroy(Department $department)
    {
        return redirect()->route('master-departments.index')->with('error', 'Please use Department Management to delete departments.');
    }
}
