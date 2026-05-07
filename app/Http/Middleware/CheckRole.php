<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'No autorizado.');
        }

        $hasAllowedRole = $user->hasAnyRole($roles);

        if (! $hasAllowedRole) {
            abort(403, 'No tienes permisos para acceder a esta seccion.');
        }

        return $next($request);
    }
}
