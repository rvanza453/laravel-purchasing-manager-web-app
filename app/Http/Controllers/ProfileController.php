<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $user->load(['roles:id,name', 'moduleRoles:id,user_id,module_key,role_name']);

        return view('profile.show', [
            'user' => $user,
            'totalModulesAccess' => $user->moduleRoles->count(),
        ]);
    }

    public function edit(): View
    {
        $user = auth()->user();
        $user->load(['roles:id,name', 'moduleRoles:id,user_id,module_key,role_name']);

        return view('profile.edit', [
            'user' => $user,
            'totalModulesAccess' => $user->moduleRoles->count(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // If new password provided, validate current password
        if (!empty($validated['password'])) {
            if (empty($validated['current_password'])) {
                return back()->withErrors(['current_password' => 'Password saat ini diperlukan untuk mengubah password.']);
            }

            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }

            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password']);

        $user->update($validated);

        return redirect()->route('global.profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function uploadSignature(Request $request): RedirectResponse
    {
        $request->validate([
            'signature' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $user = auth()->user();

        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
        }

        $path = $request->file('signature')->store('signatures', 'public');
        $user->update(['signature_path' => $path]);

        return redirect()->route('global.profile.edit')
            ->with('success', 'Tanda tangan berhasil diupload.');
    }

    public function deleteSignature(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
            $user->update(['signature_path' => null]);
        }

        return redirect()->route('global.profile.edit')
            ->with('success', 'Tanda tangan berhasil dihapus.');
    }
}
