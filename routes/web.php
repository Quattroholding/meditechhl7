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
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\AppointmentActionController;
use App\Http\Controllers\Auth\TwoFactorLoginController;
use App\Http\Controllers\DebugLoginController;
use App\Http\Controllers\EnterpriseLeadController;
use App\Http\Controllers\FirstLoginController;
use App\Http\Controllers\HemoScreenStandaloneWebController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MedicalLeaveVerificationController;
use App\Http\Controllers\PublicPatientRegistrationController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\TwoFactorEmailBackupController;
use App\Models\Appointment;
use App\Notifications\TestEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * ============================================================================
 * AUTHENTICATION ROUTES
 * ============================================================================
 */
require __DIR__.'/auth.php';

// Debug Login Routes - Solo accesible desde IPs autorizadas
Route::middleware('debug.ip')->prefix('debug')->name('debug.')->group(function () {
    Route::get('/login', [DebugLoginController::class, 'index'])
        ->name('login');

    Route::post('/login/{user}', [DebugLoginController::class, 'loginAs'])
        ->name('login.as');

    // Test Nightwatch Exception Reporting - Genera excepción única cada vez
    Route::get('/test-nightwatch', function () {
        $testId = Str::random(8);
        throw new Exception("Nightwatch Test Exception #{$testId} - Generated at ".now()->format('Y-m-d H:i:s'));
    })->name('test.nightwatch');
});

// API Documentation Routes - Solo accesible desde IPs autorizadas
Route::middleware('api.docs.ip')->prefix('api-docs')->name('api.docs.')->group(function () {
    Route::get('/', [ApiDocsController::class, 'show'])->name('index');
    Route::get('/recepy', [ApiDocsController::class, 'recepyIndex'])->name('recepy');
    Route::get('/{page}', [ApiDocsController::class, 'show'])->name('show');
});

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
    Route::get('/gateway_config', [HemoScreenStandaloneWebController::class, 'config'])->name('hemoscreen.config');
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
Route::post('/hemoscreen/demo-request', [LandingController::class, 'hemoscreenDemoRequest'])->name('hemoscreen.demo-request');

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
    ->middleware('throttle:15,60')
    ->name('public.register.store');
Route::get('/register/success', [PublicRegistrationController::class, 'success'])
    ->name('public.register.success');
Route::get('/payment/result', [PublicRegistrationController::class, 'paymentResult'])
    ->name('payment.result');

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

/**
 * ============================================================================
 * EMAIL TEST ROUTE (IP restricted)
 * ============================================================================
 */
Route::get('/test-email', function () {
    try {
        // Crear un notificable de prueba con los datos mínimos necesarios
        $testNotifiable = new class
        {
            public $email = 'rgasperi@smartcarebilling.com';

            public $first_name = 'Usuario de Prueba';

            public function routeNotificationForMail()
            {
                return $this->email;
            }
        };

        // Enviar la notificación con metadata de prueba
        Notification::send($testNotifiable, new TestEmailNotification);

        return response()->json([
            'success' => true,
            'message' => 'Correo de prueba enviado exitosamente con metadata completa',
            'from' => config('mail.from.address'),
            'to' => 'rgasperi@smartcarebilling.com',
            'time' => now()->toDateTimeString(),
            'metadata_included' => [
                'Type' => 'test-email',
                'Test-Category' => 'email-metadata-verification',
                'Test-ID' => 'TEST-'.now()->format('YmdHis'),
                'Environment' => config('app.env'),
                'IP-Address' => request()->ip() ?? '127.0.0.1',
                'Timestamp' => now()->format('Y-m-d H:i:s'),
                'plus' => '7 campos adicionales de metadata',
            ],
            'instructions' => 'Revisa el Message Trace en /email/message-trace para verificar que los headers X-* estén presentes',
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'config' => [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
            ],
        ], 500);
    }
})->middleware('restrict.ip')->name('test.email');

/**
 * ============================================================================
 * MEDICAL LEAVE VERIFICATION ROUTES (Public)
 * ============================================================================
 */
Route::get('/verificar-incapacidad/{verificationHash}', [MedicalLeaveVerificationController::class, 'verify'])
    ->name('medical-leave.verify');
