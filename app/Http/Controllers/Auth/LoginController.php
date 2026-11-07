<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
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

    public function login(LoginRequest $request): RedirectResponse
    {
        $loginId  = trim($request->input('login_id'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Jika 6 digit angka → guard siswa, field 'id_siswa'
        if (preg_match('/^\d{6}$/', $loginId)) {
            if (Auth::guard('siswa')->attempt(['id_siswa' => $loginId, 'password' => $password], $remember)) {
                $student = Auth::guard('siswa')->user();
                if (! $student->is_active) {
                    Auth::guard('siswa')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()
                        ->withErrors(['login_id' => 'Akun Anda dinonaktifkan. Hubungi admin.'])
                        ->withInput($request->except('password'));
                }
                $request->session()->regenerate();
                return redirect()->intended(route('siswa.dashboard'));
            }
            return back()
                ->withErrors(['login_id' => 'ID atau password salah.'])
                ->withInput($request->except('password'));
        }

        // Email → guard admin (web), field 'email'
        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            if (Auth::guard('web')->attempt(['email' => $loginId, 'password' => $password], $remember)) {
                $admin = Auth::guard('web')->user();
                if (! $admin->is_active) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()
                        ->withErrors(['login_id' => 'Akun Anda dinonaktifkan.'])
                        ->withInput($request->except('password'));
                }
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
            return back()
                ->withErrors(['login_id' => 'Email atau password salah.'])
                ->withInput($request->except('password'));
        }

        return back()
            ->withErrors(['login_id' => 'Format ID tidak dikenali. Masukkan 6 digit ID atau email.'])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request): RedirectResponse
    {
        // Logout dari guard mana pun
        foreach (['web', 'siswa'] as $g) {
            if (Auth::guard($g)->check()) {
                Auth::guard($g)->logout();
            }
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}
