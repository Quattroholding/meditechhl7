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

        $user = auth()->user();

        // Permitir acceso si: puede pagar suscripción O tiene algún permiso de suscripción
        $hasSubscriptionPermission = $user->canAny([
            'suscriptions.show',
            'suscriptions.manage',
            'suscriptions.invoices.index',
            'suscriptions.invoices.pending',
            'suscriptions.invoices.show',
            'suscriptions.invoices.download',
            'suscriptions.invoices.edit',
            'suscriptions.invoices.destroy',
            'suscriptions.payments.index',
            'suscriptions.payments.settings',
            'suscriptions.payments.verify',
            'suscriptions.payments.show',
            'suscriptions.payments.download',
            'suscriptions.payments.edit',
            'suscriptions.payments.destroy',
        ]);

        if (! $user->canPaySubscription() && ! $hasSubscriptionPermission) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
