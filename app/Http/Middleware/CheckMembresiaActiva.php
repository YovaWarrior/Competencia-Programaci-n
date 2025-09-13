<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMembresiaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        if (!$user->tieneMembresiaActiva()) {
            return redirect()->route('membresias.index')
                ->with('error', 'Necesitas una membresía activa para acceder a esta sección.');
        }

        return $next($request);
    }
}