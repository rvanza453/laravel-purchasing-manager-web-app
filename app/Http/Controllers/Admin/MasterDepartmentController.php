<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Site;
use App\Models\SubDepartment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterDepartmentController extends Controller
{
    public function index(Request $request)
    {
        $site_id = $request->site_id;
        
        if ($site_id) {
            $site = Site::findOrFail($site_id);
            $departments = Department::where('site_id', $site_id)->with(['subDepartments'])->orderBy('name')->get();
            return view('admin.master_departments.index', compact('departments', 'site'));
        }

        $sites = Site::withCount('departments')->orderBy('name')->get();
        return view('admin.master_departments.index', compact('sites'));
    }

    public function create()
    {
        $sites = Site::all();
        return view('admin.master_departments.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('departments')->where(function ($query) use ($request) {
                return $query->where('site_id', $request->site_id);
            })],
        ]);

        Department::create($validated);

        return redirect()->route('master-departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $master_department)
    {
        // Parameter binding might be 'master_department' due to resource name, 
        // but we can map it. Let's assume route param is 'master_department'.
        $department = $master_department; 
        
        $sites = Site::all();
        $department->load('subDepartments'); 

        return view('admin.master_departments.edit', compact('department', 'sites'));
    }

    public function update(Request $request, Department $master_department)
    {
        $department = $master_department;
        
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id', // Added site_id update capability
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('departments')->ignore($department->id)->where(function ($query) use ($department) {
                return $query->where('site_id', $department->site_id);
            })],
        ]);

        $department->update($validated);

        return redirect()->route('master-departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $master_department)
    {
        $master_department->delete();
        return redirect()->route('master-departments.index')->with('success', 'Department deleted successfully.');
    }
}
