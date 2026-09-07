<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan akun yang di-nonaktifkan admin langsung kehilangan akses,
 * bahkan jika sesi login-nya masih berjalan (tidak menunggu logout manual).
 * Dipasang di SEMUA route yang butuh auth, bukan hanya yang role-gated,
 * supaya tidak ada celah akses lewat route umum (dashboard, riwayat, dst).
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'username' => 'Akun Anda telah dinonaktifkan. Hubungi admin.',
            ]);
        }

        return $next($request);
    }
}
