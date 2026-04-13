<?php

namespace App\Http\Controllers\Admin;

use Modules\PrSystem\Models\Site;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SiteController extends AdminController
{
    public function index(): View
    {
        $sites = Site::withCount('departments')->orderBy('name')->paginate(20);
        return view('admin.sites.index', compact('sites'));
    }

    public function create(): View
    {
        return view('admin.sites.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:sites,name',
            'code'  => 'required|string|max:20|unique:sites,code',
        ]);

        Site::create($validated);

        return redirect()->route('admin.sites.index')->with('success', 'Site berhasil ditambahkan.');
    }

    public function edit(Site $site): View
    {
        return view('admin.sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('sites', 'name')->ignore($site->id)],
            'code' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('sites', 'code')->ignore($site->id)],
        ]);

        $site->update($validated);

        return redirect()->route('admin.sites.index')->with('success', 'Site berhasil diperbarui.');
    }

    public function destroy(Site $site): RedirectResponse
    {
        $site->delete();
        return redirect()->route('admin.sites.index')->with('success', 'Site berhasil dihapus.');
    }
}
