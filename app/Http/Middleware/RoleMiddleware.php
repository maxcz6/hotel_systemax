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
     * @param  string $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $userRole = Auth::user()->role;

        // Administrador and Gerente have access to everything
        if (in_array($userRole, ['administrador', 'gerente'])) {
            return $next($request);
        }

        if ($userRole !== $role) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
