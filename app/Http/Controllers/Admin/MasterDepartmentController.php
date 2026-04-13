<?php

namespace App\Http\Controllers\Admin;

use Modules\PrSystem\Models\Department;
use Modules\PrSystem\Models\Site;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class MasterDepartmentController extends AdminController
{
    public function index(Request $request): View
    {
        $site_id = $request->site_id;
        
        if ($site_id) {
            $site = Site::findOrFail($site_id);
            $departments = Department::where('site_id', $site_id)->with(['subDepartments'])->orderBy('name')->paginate(20);
            return view('admin.master-departments.index', compact('departments', 'site'));
        }

        $sites = Site::withCount('departments')->orderBy('name')->paginate(20);
        return view('admin.master-departments.index', compact('sites'));
    }

    public function create(): View
    {
        $sites = Site::all();
        return view('admin.master-departments.create', compact('sites'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'coa' => ['required', 'string', 'max:50', Rule::unique('departments')->where(function ($query) use ($request) {
                return $query->where('site_id', $request->site_id);
            })],
        ]);

        Department::create($validated);

        return redirect()->route('admin.master-departments.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(Department $master_department): View
    {
        $department = $master_department; 
        $sites = Site::all();
        $department->load('subDepartments'); 

        return view('admin.master-departments.edit', compact('department', 'sites'));
    }

    public function update(Request $request, Department $master_department): RedirectResponse
    {
        $department = $master_department;
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'coa' => ['required', 'string', 'max:50', Rule::unique('departments')->ignore($department->id)->where(function ($query) use ($department) {
                return $query->where('site_id', $department->site_id);
            })],
        ]);

        $department->update($validated);

        return redirect()->route('admin.master-departments.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Department $master_department): RedirectResponse
    {
        $master_department->delete();
        return redirect()->route('admin.master-departments.index')->with('success', 'Unit berhasil dihapus.');
    }
}
