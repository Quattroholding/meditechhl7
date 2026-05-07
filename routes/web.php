<?php

/**
 * ============================================================================
 * PUBLIC ROUTES
 * ============================================================================
 * This file contains ONLY public routes accessible without authentication.
 * Authenticated routes are organized in separate files under routes/web/
 * ============================================================================
 */

use App\Http\Controllers\Api\Recepy\RecepyPrescriptionController;
use App\Http\Controllers\AppointmentActionController;
use App\Http\Controllers\Auth\TwoFactorLoginController;
use App\Http\Controllers\EnterpriseLeadController;
use App\Http\Controllers\FirstLoginController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PublicPatientRegistrationController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\TwoFactorEmailBackupController;
use App\Models\Appointment;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

/**
 * ============================================================================
 * AUTHENTICATION ROUTES
 * ============================================================================
 */
require __DIR__.'/auth.php';

/**
 * ============================================================================
 * SUBDOMAIN ROUTES
 * ============================================================================
 */

// SAMI Subdomain (funciona con cualquier dominio base)
Route::domain('sami.{domain}')->where(['domain' => '.*'])->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('sami.home');
    Route::get('/recetas', [LandingController::class, 'recetas'])->name('sami.recetas');
    Route::get('/api/practitioners', [LandingController::class, 'getPractitioners'])->name('sami.api.practitioners');
});

// HemoScreen Subdomain
Route::domain('hemoscreen.{domain}')->where(['domain' => '.*'])->group(function () {
    Route::get('/', [LandingController::class, 'hemoscreen'])->name('hemoscreen.landing');
});

// SAMIRX Subdomain
Route::domain('samirx.{domain}')->where(['domain' => '.*'])->group(function () {
    Route::get('/', [LandingController::class, 'recetas'])->name('samirx.home');
});

/**
 * ============================================================================
 * LANDING PAGES
 * ============================================================================
 */
Route::get('/', [LandingController::class, 'welcome'])->name('welcome');
Route::get('/sami', [LandingController::class, 'index'])->name('sami');
Route::get('/sami_recetas', [LandingController::class, 'recetas'])->name('sami_recetas');
Route::get('/api/practitioners', [LandingController::class, 'getPractitioners'])->name('api.practitioners');
Route::get('/pacientes', [LandingController::class, 'patientLanding'])->name('patients.landing');

/**
 * ============================================================================
 * LEGAL PAGES
 * ============================================================================
 */
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy.policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms.service');

/**
 * ============================================================================
 * PUBLIC REGISTRATION
 * ============================================================================
 */

// Client Registration
Route::get('/register/client', [PublicRegistrationController::class, 'showForm'])
    ->name('public.register');
Route::post('/register/client', [PublicRegistrationController::class, 'store'])
    ->middleware('throttle:5,60')
    ->name('public.register.store');
Route::get('/register/success', [PublicRegistrationController::class, 'success'])
    ->name('public.register.success');

// Patient Registration (IMPORTANTE: success route debe ir ANTES de {client} para evitar conflictos)
Route::get('/register/patient/success', [PublicPatientRegistrationController::class, 'success'])
    ->name('public.patient.register.success');
Route::get('/register/patient/{client}', [PublicPatientRegistrationController::class, 'showForm'])
    ->name('public.patient.register');
Route::post('/register/patient/{client}', [PublicPatientRegistrationController::class, 'store'])
    ->middleware('throttle:15,60')
    ->name('public.patient.register.store');

/**
 * ============================================================================
 * ENTERPRISE CONTACT FORM
 * ============================================================================
 */
Route::post('/enterprise-contact', [EnterpriseLeadController::class, 'store'])
    ->middleware('throttle:enterprise-leads')
    ->name('enterprise.lead.store');

/**
 * ============================================================================
 * TWO-FACTOR AUTHENTICATION CHALLENGE
 * ============================================================================
 */
Route::get('/two-factor-challenge', [TwoFactorLoginController::class, 'show'])
    ->name('two-factor.login');
Route::post('/two-factor-challenge', [TwoFactorLoginController::class, 'verify'])
    ->name('two-factor.verify');

// Email backup codes during 2FA challenge (not authenticated yet)
Route::post('/two-factor-challenge/request-email-code', [TwoFactorEmailBackupController::class, 'requestEmailCode'])
    ->name('2fa.email-backup.request');
Route::post('/two-factor-challenge/verify-email-code', [TwoFactorEmailBackupController::class, 'verifyEmailCode'])
    ->name('2fa.email-backup.verify');

/**
 * ============================================================================
 * FIRST LOGIN (requires auth)
 * ============================================================================
 */
Route::middleware('auth')->group(function () {
    Route::get('/first-login', [FirstLoginController::class, 'show'])->name('first-login.show');
    Route::put('/first-login', [FirstLoginController::class, 'update'])->name('first-login.update');
});

/**
 * ============================================================================
 * HELP CENTER (Public)
 * ============================================================================
 */
Route::prefix('help')->name('help.')->group(function () {
    Route::get('/', function () {
        return view('help.index');
    })->name('index');
    Route::get('/registration', function () {
        return view('help.registration');
    })->name('registration');
    Route::get('/branches', function () {
        return view('help.branches');
    })->name('branches');
    Route::get('/consulting-rooms', function () {
        return view('help.consulting-rooms');
    })->name('consulting-rooms');
    Route::get('/patients', function () {
        return view('help.patients');
    })->name('patients');
    Route::get('/medical-history', function () {
        return view('help.medical-history');
    })->name('medical-history');
    Route::get('/appointments', function () {
        return view('help.appointments');
    })->name('appointments');
    Route::get('/settings', function () {
        return view('help.settings');
    })->name('settings');
    Route::get('/consultation', function () {
        return view('help.consultation');
    })->name('consultation');
    Route::get('/billing', function () {
        return view('help.billing');
    })->name('billing');
    Route::get('/payments', function () {
        return view('help.payments');
    })->name('payments');
    Route::get('/subscriptions', function () {
        return view('help.subscriptions');
    })->name('subscriptions');
    Route::get('/doctor-dashboard', function () {
        return view('help.doctor-dashboard');
    })->name('doctor-dashboard');
    Route::get('/profile', function () {
        return view('help.profile');
    })->name('profile');
    Route::get('/support', function () {
        return view('help.support');
    })->name('support');
    Route::get('/service-requests', function () {
        return view('help.service-requests');
    })->name('service-requests');
    Route::get('/medicines', function () {
        return view('help.medicines');
    })->name('medicines');
    Route::get('/medical-directory', function () {
        return view('help.medical-directory');
    })->name('medical-directory');
    Route::get('/doctors', function () {
        return view('help.doctors');
    })->name('doctors');
    Route::get('/users', function () {
        return view('help.users');
    })->name('users');
    Route::get('/2fa', function () {
        return view('help.2fa');
    })->name('2fa');
    Route::get('/roles', function () {
        $roles = Rol::whereIn('id', [5, 2, 6, 3, 4])
            ->get()
            ->sortBy(function ($role) {
                return array_search($role->id, [5, 2, 6, 3, 4]);
            });

        $excludedModules = ['roles', 'clientes', 'encuestas', 'reportes', 'aseguradoras', 'paquetes', 'hemoscreen', 'cotizaciones', 'tickets', 'dashboards'];
        $excludedPermissions = [
            'Validar usuarios registrados desde la aplicación móvil',
            'Eliminar registros de pacientes',
            'Eliminar registros de consultas',
            'Editar información de medicamentos',
            'Eliminar medicamentos del catálogo',
            'Seleccionar plantilla de recetas medicas',
            'Acceso al dashboard de contabilidad',
            'Editar factura de suscripcion',
            'Editar pago de suscripcion',
            'Cancelar pago de suscripcion',
            'Verificar pago de suscripcion',
            'Ver lista de suscripciones',
            'Comentar cambiar estauts',
            'Asignar ticket',
        ];

        $permissions = Permission::whereNotIn('module', $excludedModules)
            ->whereNotIn('description', $excludedPermissions)
            ->whereNotIn('name', $excludedPermissions)
            ->orderBy('id')
            ->get()
            ->groupBy('module');

        $matrix = DB::table('role_has_permissions')
            ->select('permission_id', 'role_id')
            ->get()
            ->groupBy('permission_id')
            ->map(function ($item) {
                return $item->pluck('role_id')->all();
            });

        return view('help.roles', compact('roles', 'permissions', 'matrix'));
    })->name('roles');
});

/**
 * ============================================================================
 * PUBLIC SURVEYS
 * ============================================================================
 */
Route::get('/survey/{token}', [SurveyController::class, 'publicForm'])->name('survey.public');
Route::post('/survey/{token}/submit', [SurveyController::class, 'submitPublic'])->name('survey.submit');

/**
 * ============================================================================
 * APPOINTMENT ACTIONS (Public with token/signed URL)
 * ============================================================================
 */

// WhatsApp Actions (Public routes with token validation)
Route::prefix('appointment-action')->name('appointment.action.')->group(function () {
    Route::get('/{appointmentId}/confirm/{token}', [AppointmentActionController::class, 'confirm'])->name('confirm');
    Route::get('/{appointmentId}/cancel/{token}', [AppointmentActionController::class, 'cancel'])->name('cancel');
});

// Email Actions (Public routes with signed URL validation for practitioners)
Route::prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/{appointment}/confirm', [AppointmentActionController::class, 'confirmSigned'])->name('confirm');
    Route::get('/{appointment}/cancel', [AppointmentActionController::class, 'cancelSigned'])->name('cancel');
});

/**
 * ============================================================================
 * PUBLIC PRESCRIPTION DOWNLOADS
 * ============================================================================
 */
Route::get('prescriptions/{id}/pdf/download', [RecepyPrescriptionController::class, 'downloadPdf']);

/**
 * ============================================================================
 * VIRTUAL CONSULTATION (Public with token)
 * ============================================================================
 */
Route::get('/join-consultation/{appointment}/{token}', function (Appointment $appointment, string $token) {
    // Verify appointment is virtual
    if (! $appointment->isVirtual()) {
        abort(404, 'Esta cita no es una teleconsulta');
    }

    // Generate expected token
    $expectedToken = hash_hmac('sha256', $appointment->id.$appointment->patient_id, config('app.key'));

    // Verify token
    if (! hash_equals($expectedToken, $token)) {
        abort(403, 'Enlace inválido o expirado');
    }

    return view('virtual-consultation.patient-join', compact('appointment'));
})->name('virtual-consultation.join');
