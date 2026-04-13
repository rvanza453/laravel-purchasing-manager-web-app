<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Modules\ServiceAgreementSystem\Models\Block;
use Modules\ServiceAgreementSystem\Models\SubDepartment;

class BlockController extends AdminController
{
    public function index(): View
    {
        $blocks = Block::with(['subDepartment.department.site'])
                    ->orderBy('name')
                    ->paginate(25);

        return view('admin.blocks.index', compact('blocks'));
    }

    public function create(): View
    {
        $subDepartments = SubDepartment::with('department.site')
                            ->orderBy('name')
                            ->get()
                            ->groupBy('department.site.name');

        return view('admin.blocks.create', compact('subDepartments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sub_department_id' => 'required|exists:sub_departments,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('blocks')->where(function ($query) use ($request) {
                    return $query->where('sub_department_id', $request->input('sub_department_id'));
                }),
            ],
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Block::create($validated);

        return redirect()
            ->route('admin.blocks.index')
            ->with('success', 'Block berhasil ditambahkan.');
    }

    public function edit(Block $block): View
    {
        $subDepartments = SubDepartment::with('department.site')
                            ->orderBy('name')
                            ->get()
                            ->groupBy('department.site.name');

        return view('admin.blocks.edit', compact('block', 'subDepartments'));
    }

    public function update(Request $request, Block $block): RedirectResponse
    {
        $validated = $request->validate([
            'sub_department_id' => 'required|exists:sub_departments,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('blocks')->where(function ($query) use ($request) {
                    return $query->where('sub_department_id', $request->input('sub_department_id'));
                })->ignore($block->id),
            ],
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $block->update($validated);

        return redirect()
            ->route('admin.blocks.index')
            ->with('success', 'Block berhasil diperbarui.');
    }

    public function destroy(Block $block): RedirectResponse
    {
        try {
            $blockName = $block->name;
            $block->delete();

            return redirect()
                ->route('admin.blocks.index')
                ->with('success', "Block \"$blockName\" berhasil dihapus.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.blocks.index')
                ->with('error', 'Tidak dapat menghapus block. Mungkin masih ada data terkait atau terjadi error.');
        }
    }
}
