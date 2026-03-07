<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Manejar la solicitud entrante.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            // Si el usuario no tiene el rol, redirigir con alerta
            return redirect()->route('root')->with('error', 'No tiene permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
