<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManageSubscription
{
    /**
     * Handle an incoming request.
     *
     * Verifica que el usuario autenticado tenga permiso para gestionar suscripciones
     * usando el método canPaySubscription del modelo User.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (! auth()->check()) {
            abort(401, 'No autenticado.');
        }

        // Verificar que el usuario pueda gestionar suscripciones
        if (! auth()->user()->canPaySubscription()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
