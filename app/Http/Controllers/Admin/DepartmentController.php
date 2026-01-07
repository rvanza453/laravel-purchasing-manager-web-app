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
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $sites = Site::all();
        return view('admin.departments.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('departments')->where(function ($query) use ($request) {
                return $query->where('site_id', $request->site_id);
            })],
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        $sites = Site::all();
        $users = User::where('department_id', $department->id)->orWhere('site_id', $department->site_id)->get(); // Users in same site/dept
        if ($users->isEmpty()) {
             $users = User::all(); // Fallback if no users assigned yet
        }
        
        return view('admin.departments.edit', compact('department', 'sites', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('departments')->ignore($department->id)->where(function ($query) use ($department) {
                return $query->where('site_id', $department->site_id);
            })],
            'approvers' => 'array',
            'approvers.*.user_id' => 'required|exists:users,id',
            'approvers.*.role_name' => 'required|string',
            'approvers.*.level' => 'required|integer',
        ]);

        $department->update(['name' => $request->name, 'code' => $request->code]);

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

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
