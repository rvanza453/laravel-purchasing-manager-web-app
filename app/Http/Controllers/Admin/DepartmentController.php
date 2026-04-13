<?php

namespace App\Http\Controllers\Admin;

use Modules\PrSystem\Models\Department;
use Modules\PrSystem\Models\Site;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DepartmentController extends AdminController
{
    public function index(): View
    {
        $departments = Department::with(['site'])
                        ->orderBy('name')
                        ->paginate(20);
        
        return view('admin.departments.index', compact('departments'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.master-departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('admin.master-departments.index');
    }

    public function edit(Department $department): RedirectResponse
    {
        return redirect()->route('admin.master-departments.edit', $department);
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        return redirect()->route('admin.departments.index')->with('success', 'Konfigurasi department berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        return redirect()->route('admin.master-departments.index')->with('error', 'Silakan gunakan Master Department untuk menghapus department.');
    }

    public function getDepartmentsBySite(Site $site)
    {
        return response()->json($site->departments()->select('id', 'name')->orderBy('name')->get());
    }
}
