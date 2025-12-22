<?php

use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Force HTTPS in production
        $middleware->append(TrustProxies::class);
        // Enable Sanctum's stateful API authentication
        $middleware->statefulApi();

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'first.login' => \App\Http\Middleware\FirstLoginMiddleware::class,
            'custom.permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'api.token' => \App\Http\Middleware\ApiTokenMiddleware::class,
            'concurrent.session' => \App\Http\Middleware\DetectConcurrentSession::class,

        ]);

        // Agregar middleware de tema del cliente a todas las rutas web
        $middleware->web(append: [
            \App\Http\Middleware\CheckActiveUserMiddleware::class,
        ]);

        // Exclude Twilio webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'api/webhooks/twilio/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function ($schedule) {
        // Sincronizar medicamentos de FDA cada mes (primer día del mes a las 2:00 AM)
        $schedule->command('medicines:sync-fda')
            ->monthlyOn(1, '02:00')
            ->description('Sincronización mensual de medicamentos con FDA')
            ->emailOutputOnFailure('business@meditecpty.com')
            ->appendOutputTo(storage_path('logs/fda-sync.log'));

        // También permitir ejecución manual para testing
        $schedule->command('medicines:sync-fda --force')
            ->weeklyOn(0, '03:00') // Domingos a las 3:00 AM para pruebas
            ->when(fn () => config('app.env') !== 'production')
            ->description('Sincronización de prueba semanal (solo en desarrollo)')
            ->appendOutputTo(storage_path('logs/fda-sync-test.log'));

        // === Tareas de Suscripciones ===

        // Generar facturas para suscripciones que deben renovarse
        $schedule->command('subscriptions:generate-invoices')
            ->dailyAt('01:00')
            ->description('Generar facturas de suscripciones pendientes de renovación')
            ->emailOutputOnFailure('business@meditecpty.com')
            ->appendOutputTo(storage_path('logs/subscription-invoices.log'));

        // Procesar trials que han expirado
        $schedule->command('subscriptions:process-trials')
            ->dailyAt('01:30')
            ->description('Procesar suscripciones de prueba que han vencido')
            ->emailOutputOnFailure('business@meditecpty.com')
            ->appendOutputTo(storage_path('logs/subscription-trials.log'));

        // Marcar facturas vencidas y suspender suscripciones si es necesario
        $schedule->command('subscriptions:process-overdue')
            ->dailyAt('02:00')
            ->description('Procesar facturas vencidas y suspender suscripciones morosas')
            ->emailOutputOnFailure('business@meditecpty.com')
            ->appendOutputTo(storage_path('logs/subscription-overdue.log'));

        // Limpiar suscripciones expiradas y códigos de referral vencidos
        $schedule->command('subscriptions:cleanup')
            ->dailyAt('03:00')
            ->description('Limpiar suscripciones expiradas y códigos de referral vencidos')
            ->emailOutputOnFailure('business@meditecpty.com')
            ->appendOutputTo(storage_path('logs/subscription-cleanup.log'));
    })
    ->create();
