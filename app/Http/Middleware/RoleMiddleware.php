<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  ...$roles  Daftar role yang diizinkan (misal: 'admin', 'supervisor')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika user tidak login atau tidak punya role yang diizinkan
        if (! Auth::check() || ! in_array(Auth::user()->role, $roles)) {
            // Kembalikan ke dashboard dengan error
            // abort(403, 'Akses ditolak. Anda tidak memiliki hak akses.');
            return redirect('/dashboard')->with('error', 'Akses ditolak.');
        }

        return $next($request);
    }
}
