<?php

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
        ]);

        // Agregar middleware de tema del cliente a todas las rutas web
        $middleware->web(append: [
            \App\Http\Middleware\CheckActiveUserMiddleware::class,
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
            ->emailOutputOnFailure('admin@meditech.com')
            ->appendOutputTo(storage_path('logs/fda-sync.log'));

        // También permitir ejecución manual para testing
        $schedule->command('medicines:sync-fda --force')
            ->weeklyOn(0, '03:00') // Domingos a las 3:00 AM para pruebas
            ->when(fn () => config('app.env') !== 'production')
            ->description('Sincronización de prueba semanal (solo en desarrollo)')
            ->appendOutputTo(storage_path('logs/fda-sync-test.log'));
    })
    ->create();
