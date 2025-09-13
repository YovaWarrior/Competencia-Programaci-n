<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder.');
        }

        if (!auth()->user()->esAdmin()) {
            abort(403, 'Acceso denegado. Solo administradores pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
