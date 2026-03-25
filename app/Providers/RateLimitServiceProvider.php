<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('enterprise-leads', function (Request $request) {
            // Si el usuario está autenticado, permitir más solicitudes
            if ($request->user()) {
                return Limit::perHour(10)->by($request->user()->id);
            }

            // Para usuarios no autenticados, usar IP y límite más bajo
            return Limit::perHour(3)->by($request->ip());
        });
    }
}
