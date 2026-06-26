<?php

use App\Http\Middleware\ApiDocsIpRestriction;
use App\Http\Middleware\ApiTokenMiddleware;
use App\Http\Middleware\CanManageSubscription;
use App\Http\Middleware\CheckActiveUserMiddleware;
use App\Http\Middleware\DebugIpRestriction;
use App\Http\Middleware\DetectConcurrentSession;
use App\Http\Middleware\EnsureTwoFactorIsEnabled;
use App\Http\Middleware\FirstLoginMiddleware;
use App\Http\Middleware\LogPatientAccess;
use App\Http\Middleware\RestrictByIp;
use App\Http\Middleware\SetLocaleMiddleware;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\WhatsappClientFilter;
use App\Jobs\RetryFailedSubscriptionPayments;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Livewire\Exceptions\ComponentNotFoundException;
use Livewire\Mechanisms\HandleComponents\CorruptComponentPayloadException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // CyberSource Payment Routes
            Route::middleware('web')
                ->group(base_path('routes/cybersource.php'));

            // Webhook Routes (accessed via webhooks.meditecpty.com subdomain)
            // No middleware - webhooks don't need authentication or session
            Route::group([], base_path('routes/webhooks.php'));

            // === Web Routes Organizadas por Dominio ===
            // Orden lógico: dashboards primero, luego features

            Route::middleware('web')
                ->group(base_path('routes/web/dashboard.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/practitioners.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/patients.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/clinical.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/inventory.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/users.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/organization.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/accounting.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/subscriptions.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/settings.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/web/help.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Force HTTPS in production
        $middleware->append(TrustProxies::class);
        // Enable Sanctum's stateful API authentication
        $middleware->statefulApi();

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'first.login' => FirstLoginMiddleware::class,
            'custom.permission' => App\Http\Middleware\PermissionMiddleware::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'api.token' => ApiTokenMiddleware::class,
            'concurrent.session' => DetectConcurrentSession::class,
            'debug.ip' => DebugIpRestriction::class,
            'api.docs.ip' => ApiDocsIpRestriction::class,
            'whatsapp.client' => WhatsappClientFilter::class,
            'can.manage.subscription' => CanManageSubscription::class,
            '2fa.enforce' => EnsureTwoFactorIsEnabled::class,
            'restrict.ip' => RestrictByIp::class,
            'log.patient.access' => LogPatientAccess::class,
        ]);

        // Agregar middleware de tema del cliente a todas las rutas web
        $middleware->web(append: [
            SetLocaleMiddleware::class,
            CheckActiveUserMiddleware::class,
            EnsureTwoFactorIsEnabled::class,
        ]);

        // Add WhatsApp client filter to API routes
        $middleware->api(append: [
            WhatsappClientFilter::class,
        ]);

        // Exclude webhooks from CSRF verification
        // These are accessed via webhooks.meditecpty.com subdomain
        $middleware->validateCsrfTokens(except: [
            'whatsapp',
            'whatsapp/*',
            'neopayments',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Don't report Livewire exceptions caused by session expiration or stale browser cache
        $exceptions->dontReport([
            CorruptComponentPayloadException::class,
            ComponentNotFoundException::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
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

        // === Tareas de Citas (Appointments) ===

        // Marcar como noshow las citas sin completar después de 7 días
        $schedule->command('appointments:mark-noshow')
            ->dailyAt('04:00')
            ->description('Marcar como noshow las citas propuestas, reservadas, pendientes o confirmadas que no se completaron después de 7 días')
            ->emailOutputOnFailure('business@meditecpty.com')
            ->appendOutputTo(storage_path('logs/appointments-noshow.log'));

        $schedule->job(new RetryFailedSubscriptionPayments)->hourly();
    })
    ->create();
