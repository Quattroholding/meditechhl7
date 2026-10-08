<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountingEnabled
{
    /**
     * Handle an incoming request.
     * Verifica que el cliente del usuario tenga contabilidad habilitada.
     * Retorna 403 si accounting_enabled = false
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        // Get user's current client
        $client = $user->getCurrentClient();

        // Check if accounting is enabled for the client
        if (! $client?->accounting_enabled) {
            abort(403, 'El módulo de contabilidad no está habilitado para tu cliente.');
        }

        return $next($request);
    }
}
