<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsEnabled
{
    /**
     * Routes that should be excluded from 2FA enforcement.
     */
    protected array $except = [
        'user/two-factor-authentication',
        'user/two-factor-qr-code',
        'user/two-factor-recovery-codes',
        'user/confirmed-two-factor-authentication',
        'user/confirmed-password-status',
        'user/confirm-password',
        'two-factor-challenge',
        'two-factor-settings',
        'test-2fa',
        'logout',
        'profile',
        'user-profile',
        'livewire/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if 2FA is globally disabled
        if (! config('two-factor.enabled', true)) {
            return $next($request);
        }

        // Skip if user is not authenticated
        if (! auth()->check()) {
            return $next($request);
        }

        // Skip if route is excluded
        if ($this->isExcluded($request)) {
            return $next($request);
        }

        $user = auth()->user();

        // Check if user's role requires 2FA
        if ($user->requiresTwoFactor() && ! $user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.settings')
                ->with('error', 'Su rol requiere autenticación de dos factores. Debe configurarla antes de continuar.');
        }

        return $next($request);
    }

    /**
     * Determine if the request is for an excluded route.
     */
    protected function isExcluded(Request $request): bool
    {
        // Check current path
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        // Check if this is a Livewire request from an excluded page
        if ($request->header('X-Livewire')) {
            $referer = $request->header('referer');
            if ($referer) {
                $path = parse_url($referer, PHP_URL_PATH);
                if ($path) {
                    $path = ltrim($path, '/');
                    foreach ($this->except as $pattern) {
                        $pattern = str_replace('*', '.*', $pattern);
                        if (preg_match('#^'.$pattern.'$#', $path)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
