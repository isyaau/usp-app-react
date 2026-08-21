<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Pengguna belum login
        }

        if ($role && !in_array(Auth::user()->role, (array) $role)) {
            return redirect()->route('unauthorized'); // Halaman jika peran tidak cocok, bisa disesuaikan
        }

        return $next($request); // Akses diberikan jika login dan peran cocok
    }
}
