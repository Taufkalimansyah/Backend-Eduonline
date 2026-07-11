<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware sederhana untuk membatasi endpoint per role,
 * didaftarkan di bootstrap/app.php sebagai alias 'role'.
 * Contoh pemakaian di routes: ->middleware('role:admin')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke resource ini.');
        }

        return $next($request);
    }
}
