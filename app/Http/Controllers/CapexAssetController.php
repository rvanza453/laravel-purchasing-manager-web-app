<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CapexAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = \App\Models\CapexAsset::latest()->get();
        return view('admin.capex.assets.index', compact('assets'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        \App\Models\CapexAsset::create($validated);

        return redirect()->route('admin.capex.assets.index')->with('success', 'Asset Created Successfully');
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\CapexAsset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $asset->update($validated);

        return redirect()->route('admin.capex.assets.index')->with('success', 'Asset Updated Successfully');
    }

    public function destroy(\App\Models\CapexAsset $asset)
    {
        $asset->delete();
        return redirect()->route('admin.capex.assets.index')->with('success', 'Asset Deleted Successfully');
    }
}
