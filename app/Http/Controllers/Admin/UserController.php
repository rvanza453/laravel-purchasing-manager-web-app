<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::with(['roles', 'site', 'department'])
                    ->orderBy('name')
                    ->get()
                    ->groupBy(function($user) {
                        return $user->site->name ?? 'No Site';
                    });
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $sites = \App\Models\Site::all();
        $departments = \App\Models\Department::all();
        return view('admin.users.create', compact('roles', 'sites', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'site_id' => 'nullable|exists:sites,id',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'site_id' => $request->site_id,
            'department_id' => $request->department_id,
            'position' => $request->position,
            'phone_number' => $request->phone_number,
        ]);

        $user->assignRole($request->role);

        \App\Helpers\ActivityLogger::log('created', 'Created user: ' . $user->name, $user);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(\App\Models\User $user)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $sites = \App\Models\Site::all();
        
        // Filter departments based on user's site
        if ($user->site_id) {
            $departments = \App\Models\Department::where('site_id', $user->site_id)->orderBy('name')->get();
        } else {
            $departments = \Illuminate\Database\Eloquent\Collection::make(); // Empty if no site
        }
        
        return view('admin.users.edit', compact('user', 'roles', 'sites', 'departments'));
    }

    public function update(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'site_id' => 'nullable|exists:sites,id',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'site_id' => $request->site_id,
            'department_id' => $request->department_id,
            'position' => $request->position,
            'phone_number' => $request->phone_number,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        \App\Helpers\ActivityLogger::log('updated', 'Updated user: ' . $user->name, $user);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(\App\Models\User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }
        $name = $user->name;
        $user->delete();
        
        \App\Helpers\ActivityLogger::log('deleted', 'Deleted user: ' . $name);

        return back()->with('success', 'User deleted successfully.');
    }
    
    public function impersonate(\App\Models\User $user, \Illuminate\Http\Request $request)
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Only admin can impersonate users.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        // Verify password
        $password = $request->input('admin_password');
        if ($password !== config('app.admin_verification_password')) {
            return back()->with('error', 'Password verifikasi salah!');
        }

        session(['impersonate_admin_id' => auth()->id()]);
        
        \Illuminate\Support\Facades\Auth::login($user);

        \App\Helpers\ActivityLogger::log('impersonated', 'Admin impersonated user: ' . $user->name, $user);

        return redirect()->route('dashboard')->with('success', 'Now logged in as ' . $user->name);
    }

    public function leaveImpersonate()
    {
        if (!session()->has('impersonate_admin_id')) {
            return redirect()->route('dashboard')->with('error', 'You are not impersonating anyone.');
        }

        $adminId = session('impersonate_admin_id');
        $currentUser = auth()->user();
        
        session()->forget('impersonate_admin_id');
        
        $admin = \App\Models\User::findOrFail($adminId);
        \Illuminate\Support\Facades\Auth::login($admin);

        \App\Helpers\ActivityLogger::log('left-impersonation', 'Admin left impersonation of user: ' . $currentUser->name);

        return redirect()->route('users.index')->with('success', 'Returned to admin account.');
    }
}
