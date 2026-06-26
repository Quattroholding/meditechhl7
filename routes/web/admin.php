<?php

/*
|--------------------------------------------------------------------------
| Admin & System Routes
|--------------------------------------------------------------------------
|
| Propósito: Rutas administrativas, reportes, tokens API, leads empresariales,
|           HemoScreen standalone, debug login y APIs internas
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Controladores Principales:
| - EnterpriseLeadController
| - ApiTokenController
| - ReportController
| - HemoScreenStandaloneWebController
| - HemoScreenExportController
| - DebugLoginController
| - ApiController
|
| Prefijos de Rutas: /admin, /reports, /api-tokens, /hemoscreen, /debug, /api
|
*/

use App\Events\AppointmentCheckedIn;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\DebugLoginController;
use App\Http\Controllers\EnterpriseLeadController;
use App\Http\Controllers\HemoScreenExportController;
use App\Http\Controllers\HemoScreenStandaloneWebController;
use App\Http\Controllers\ReportController;
use App\Livewire\Admin\PatientAccessAuditLog;
use App\Models\Appointment;
use Illuminate\Support\Facades\Route;

// ============================================================================
// ENTERPRISE LEADS (Admin - Protegido)
// ============================================================================

// ============================================================================
// PATIENT ACCESS AUDIT LOG (Compliance - Admin only)
// ============================================================================

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin/compliance')->group(function () {
    Route::get('/patient-access-audit', PatientAccessAuditLog::class)
        ->name('compliance.patient.access.audit');
});

// ============================================================================
// ENTERPRISE LEADS (Admin - Protegido)
// ============================================================================

Route::middleware(['auth', 'verified'])->prefix('admin/leads')->group(function () {
    Route::get('/', [EnterpriseLeadController::class, 'index'])->name('leads.index');
    Route::get('/{id}', [EnterpriseLeadController::class, 'show'])->name('leads.show');
    Route::post('/{id}/update', [EnterpriseLeadController::class, 'update'])->name('leads.update');
    Route::post('/{id}/convert', [EnterpriseLeadController::class, 'convertToClient'])->name('leads.convert');
});

// ============================================================================
// API TOKENS (Admin only)
// ============================================================================

Route::middleware(['auth', 'first.login', 'role:admin'])->group(function () {
    Route::resource('api-tokens', ApiTokenController::class);
    Route::post('/api-tokens/{apiToken}/toggle', [ApiTokenController::class, 'toggle'])->name('api-tokens.toggle');
    Route::post('/api-tokens/{apiToken}/regenerate', [ApiTokenController::class, 'regenerate'])->name('api-tokens.regenerate');
});

// ============================================================================
// REPORTS
// ============================================================================

Route::middleware(['auth', 'first.login'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/{report}', [ReportController::class, 'show'])->name('show');
    Route::post('/{report}/excel', [ReportController::class, 'excel'])->name('excel');
    Route::post('/{report}/pdf', [ReportController::class, 'pdf'])->name('pdf');
});

// ============================================================================
// HEMOSCREEN STANDALONE
// ============================================================================

Route::middleware(['auth', 'role:hemoscreen'])->prefix('hemoscreen')->name('hemoscreen.')->group(function () {
    Route::get('/dashboard', [HemoScreenStandaloneWebController::class, 'index'])->name('dashboard');
    Route::get('/results/{resultId}', [HemoScreenStandaloneWebController::class, 'show'])->name('results.show');
    Route::get('/export-pdf', [HemoScreenExportController::class, 'exportPdf'])->name('export-pdf');
    Route::get('/export-single-pdf/{result}', [HemoScreenExportController::class, 'exportSinglePdf'])->name('export-single-pdf');
    Route::get('/export-csv', [HemoScreenExportController::class, 'exportCsv'])->name('export-csv');
});

// ============================================================================
// DEBUG LOGIN (Solo accesible desde IPs autorizadas)
// ============================================================================

Route::middleware('debug.ip')->prefix('debug')->name('debug.')->group(function () {
    Route::get('/login', [DebugLoginController::class, 'index'])
        ->name('login');

    Route::post('/login/{user}', [DebugLoginController::class, 'loginAs'])
        ->name('login.as');
});

// ============================================================================
// INTERNAL APIs
// ============================================================================

Route::group(['prefix' => 'api'], function () {

    Route::get('/diagnostics', [ApiController::class, 'diagnostics'])->name('api.diagnostics');
    Route::get('/cpts/{type}', [ApiController::class, 'cpts'])->name('api.cpts');
    Route::get('/medical_speciality', [ApiController::class, 'medicalSpeciality'])->name('api.medical_speciality');
    Route::get('/medicines', [ApiController::class, 'medicines'])->name('api.medicines');
    Route::get('/patients', [ApiController::class, 'patients'])->name('api.patients');
    Route::get('/users', [ApiController::class, 'users'])->name('api.users');
    // Route::get('/practitioners', [ApiController::class, 'practitioners'])->name('api.practitioners');
    Route::get('/services_catalog', [ApiController::class, 'servicesCatalog'])->name('api.servicesCatalog');
    Route::get('/states/{country_id}', [ApiController::class, 'statesByCountry'])->name('api.states');

    // Doctor notifications API
    Route::get('/doctor-notifications/{doctor_id}', function ($doctor_id) {
        $notifications = [];

        // Verificar notificación en sesión (fallback para mismo navegador)
        $sessionKey = 'doctor_notification_'.$doctor_id;
        if (session()->has($sessionKey)) {
            $notification = session()->get($sessionKey);

            // Solo mostrar si es reciente (últimos 2 minutos)
            if ($notification['timestamp'] > (now()->timestamp - 120)) {
                $notifications[] = $notification;
            }

            // Limpiar la notificación después de entregarla
            session()->forget($sessionKey);
        }

        return response()->json(['notifications' => $notifications]);
    })->middleware('auth')->name('api.doctor.notifications');

});

// ============================================================================
// TEST BROADCAST
// ============================================================================

Route::get('/test-broadcast/{appointment_id}', function ($appointment_id) {
    $appointment = Appointment::find($appointment_id);
    if ($appointment) {
        broadcast(new AppointmentCheckedIn($appointment));

        return 'Broadcast sent for appointment '.$appointment_id.' to doctor '.$appointment->practitioner_id;
    }

    return 'Appointment not found';
})->middleware('auth')->name('test.broadcast');
