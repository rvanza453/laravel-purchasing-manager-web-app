<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\ServiceAgreementSystem\Models\Department;
use Modules\ServiceAgreementSystem\Models\Site;
use Spatie\Permission\Models\Role;

class UserController extends AdminController
{
    public function index(): View
    {
        $users = User::query()
            ->with(['roles:id,name', 'moduleRoles:id,user_id,module_key,role_name'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
            'moduleRoleConfig' => config('module-roles.modules', []),
        ]);
    }

    public function create(): View
    {
        $sites = Site::query()->orderBy('name')->get(['id', 'name']);
        $departments = Department::query()->orderBy('name')->get(['id', 'name', 'site_id']);

        return view('admin.users.create', [
            'user' => new User(),
            'sites' => $sites,
            'departments' => $departments,
            'spatieRoles' => Role::query()->orderBy('name')->pluck('name'),
            'moduleRoleConfig' => config('module-roles.modules', []),
            'selectedGlobalRole' => null,
            'selectedModuleRoles' => [],
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $user = User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'site_id' => $validated['site_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'position' => $validated['position'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        $this->syncRoles($user, $validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $user->load(['roles:id,name', 'moduleRoles:id,user_id,module_key,role_name']);
        $sites = Site::query()->orderBy('name')->get(['id', 'name']);
        $departments = Department::query()->orderBy('name')->get(['id', 'name', 'site_id']);

        return view('admin.users.edit', [
            'user' => $user,
            'sites' => $sites,
            'departments' => $departments,
            'spatieRoles' => Role::query()->orderBy('name')->pluck('name'),
            'moduleRoleConfig' => config('module-roles.modules', []),
            'selectedGlobalRole' => optional($user->roles->first())->name,
            'selectedModuleRoles' => $user->moduleRoles->pluck('role_name', 'module_key')->toArray(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validatePayload($request, $user->id);

        $payload = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'site_id' => $validated['site_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'position' => $validated['position'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);
        $this->syncRoles($user, $validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    public function impersonate(User $user, Request $request): RedirectResponse
    {
        if (!auth()->user()?->hasRole('Admin')) {
            abort(403, 'Only admin can impersonate users.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat impersonate akun sendiri.');
        }

        $password = (string) $request->input('admin_password', '');
        $verificationPassword = (string) config('prsystem.app.admin_verification_password', config('app.admin_verification_password'));

        if ($password !== $verificationPassword) {
            return back()->with('error', 'Password verifikasi salah.');
        }

        session(['impersonate_admin_id' => auth()->id()]);
        Auth::login($user);

        return redirect()->route('modules.index')->with('success', 'Berhasil login sebagai ' . $user->name . '.');
    }

    public function leaveImpersonate(): RedirectResponse
    {
        if (!session()->has('impersonate_admin_id')) {
            return redirect()->route('modules.index')->with('error', 'Tidak ada sesi impersonate aktif.');
        }

        $adminId = (int) session('impersonate_admin_id');
        session()->forget('impersonate_admin_id');

        $admin = User::query()->find($adminId);
        if (!$admin) {
            return redirect()->route('modules.index')->with('error', 'Akun admin asal tidak ditemukan.');
        }

        Auth::login($admin);

        return redirect()->route('admin.users.index')->with('success', 'Kembali ke akun admin.');
    }

    private function validatePayload(Request $request, ?int $ignoreUserId = null): array
    {
        $moduleRoleConfig = config('module-roles.modules', []);
        $moduleKeys = array_keys($moduleRoleConfig);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($ignoreUserId),
            ],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoreUserId)],
            'password' => [$ignoreUserId ? 'nullable' : 'required', 'string', 'min:6'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'global_role' => ['nullable', 'string', 'exists:roles,name'],
            'module_roles' => ['nullable', 'array'],
        ];

        foreach ($moduleKeys as $moduleKey) {
            $roles = Arr::get($moduleRoleConfig, $moduleKey . '.roles', []);
            $rules['module_roles.' . $moduleKey] = ['nullable', 'string', 'in:' . implode(',', $roles)];
        }

        return $request->validate($rules);
    }

    private function syncRoles(User $user, array $validated): void
    {
        $globalRole = $validated['global_role'] ?? null;
        $moduleRoles = array_filter($validated['module_roles'] ?? []);

        $user->syncRoles($globalRole ? [$globalRole] : []);
        $user->syncModuleRoles($moduleRoles);
    }
}
