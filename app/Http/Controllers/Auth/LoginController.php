<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);

        $loginField = filter_var($validated['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginField => $validated['login'],
            'password' => $validated['password'],
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'login' => 'Email/Username atau password salah.',
            ])->onlyInput('login');
        }

        $user = Auth::user();
        $hasGlobalRole = $user && method_exists($user, 'roles') && $user->roles()->exists();
        $hasModuleRole = $user && method_exists($user, 'moduleRoles') && $user->moduleRoles()->exists();

        if (!$hasGlobalRole && !$hasModuleRole) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'login' => 'Akun Anda belum memiliki role. Hubungi administrator untuk mendapatkan akses.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('modules.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
