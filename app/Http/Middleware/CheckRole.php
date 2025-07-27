<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika pengguna tidak login, redirect ke halaman login
        if (!$request->user()) {
            return redirect('/login');
        }

        // Jika pengguna memiliki role yang diizinkan, lanjutkan request
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki izin, redirect ke dashboard dengan pesan error
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
    }
}