<?php

namespace App\Http\Controllers\Admin;

use Modules\PrSystem\Models\SubDepartment;
use Modules\PrSystem\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class SubDepartmentController extends AdminController
{
    public function index(): View
    {
        $subDepartments = SubDepartment::with(['department.site'])
                        ->orderBy('name')
                        ->paginate(20);
        
        return view('admin.sub-departments.index', compact('subDepartments'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.sub-departments.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'coa' => ['nullable', 'string', 'max:50', Rule::unique('sub_departments', 'coa')->where(function ($query) use ($request) {
                return $query->where('department_id', $request->department_id);
            })],
        ]);

        SubDepartment::create($validated);

        return redirect()->route('admin.sub-departments.index')->with('success', 'Sub Department berhasil ditambahkan.');
    }

    public function edit(SubDepartment $sub_department): View
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.sub-departments.edit', compact('sub_department', 'departments'));
    }

    public function update(Request $request, SubDepartment $sub_department): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'coa' => ['nullable', 'string', 'max:50', Rule::unique('sub_departments', 'coa')->ignore($sub_department->id)->where(function ($query) use ($request) {
                return $query->where('department_id', $request->department_id);
            })],
        ]);

        $sub_department->update($validated);

        return redirect()->route('admin.sub-departments.index')->with('success', 'Sub Department berhasil diperbarui.');
    }

    public function destroy(SubDepartment $sub_department): RedirectResponse
    {
        $sub_department->delete();
        return redirect()->route('admin.sub-departments.index')->with('success', 'Sub Department berhasil dihapus.');
    }
}
