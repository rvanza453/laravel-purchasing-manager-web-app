<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GlobalApproverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $approvers = \App\Models\GlobalApproverConfig::with('user')->orderBy('level')->get();
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.global_approvers.index', compact('approvers', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|string',
            'level' => 'required|integer|unique:global_approver_configs,level',
        ]);

        \App\Models\GlobalApproverConfig::create($request->all());

        return redirect()->route('global-approvers.index')->with('success', 'Global Approver added successfully.');
    }

    public function destroy(\App\Models\GlobalApproverConfig $globalApprover)
    {
        $globalApprover->delete();
        return back()->with('success', 'Global Approver removed successfully.');
    }
}
