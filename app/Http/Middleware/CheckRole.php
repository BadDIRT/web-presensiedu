<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Coba guard admin dulu, kalau tidak ada coba siswa
        $guard = Auth::guard('web')->check() ? 'web' : (Auth::guard('siswa')->check() ? 'siswa' : null);

        if (! $guard) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::guard($guard)->user();

        // Akun nonaktif?
        if (method_exists($user, 'isAktif') && ! $user->isAktif()) {
            Auth::guard($guard)->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('error', 'Akun Anda dinonaktifkan. Hubungi admin.');
        }

        // Tentukan role user: admin dari guard 'web', siswa dari guard 'siswa'
        $userRole = $guard === 'web'
            ? $user->role  // admin punya kolom role
            : 'siswa';     // siswa selalu 'siswa'

        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}
