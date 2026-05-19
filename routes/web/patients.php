<?php

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
|
| Propósito: Gestión de pacientes (CRUD, perfil, historial médico, aseguradoras, exportación de historia)
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Controladores Principales:
| - PatientController
| - PatientHistoryController (exportación de historia clínica)
|
| Prefijos de Rutas: /patients, /patient-history
|
*/

use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientHistoryController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// CRUD DE PATIENTS
// ============================================================================

Route::group(['prefix' => 'patients', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [PatientController::class, 'index'])
        ->middleware('permission:patients.view')
        ->name('patient.index');

    Route::get('/create', [PatientController::class, 'create'])
        ->middleware('permission:patients.create')
        ->name('patient.create');

    Route::post('/store', [PatientController::class, 'store'])
        ->middleware('permission:patients.create')
        ->name('patient.store');

    Route::get('/check/{id_number}', [PatientController::class, 'check'])
        ->name('patient.check');

    Route::post('/associate', [PatientController::class, 'associate'])
        ->middleware('permission:patients.create')
        ->name('patient.associate');

    Route::get('/{id}/profile', [PatientController::class, 'profile'])
        ->middleware('permission:patients.profile')
        ->name('patient.profile');

    Route::get('/{id}/insurances', [PatientController::class, 'insurances'])
        ->name('patient.insurances');

    Route::get('/{id}/medical_history', [PatientController::class, 'medicalHistory'])
        ->middleware('permission:patients.medical_history')
        ->name('patient.medical_history');

    Route::get('/{id}/edit', [PatientController::class, 'edit'])
        ->middleware('permission:patients.edit')
        ->name('patient.edit');

    Route::get('/{id}', [PatientController::class, 'show'])
        ->middleware('permission:patients.edit')
        ->name('patient.show');

    Route::put('/{id}/update', [PatientController::class, 'update'])
        ->middleware('permission:patients.update')
        ->name('patient.update');

    Route::delete('/{id}', [PatientController::class, 'destroy'])
        ->middleware('permission:patients.delete')
        ->name('patient.destroy');

});

// ============================================================================
// PATIENT HISTORY EXPORT ROUTES
// ============================================================================

// Patient History Download Routes - Exportación asíncrona de historia clínica completa
Route::middleware(['auth'])->prefix('patient-history')->name('patient.history.')->group(function () {

    Route::post('/{patient}/generate', [PatientHistoryController::class, 'generate'])
        ->name('generate');

    Route::get('/{id}/status', [PatientHistoryController::class, 'status'])
        ->name('status');

    Route::get('/{id}/download', [PatientHistoryController::class, 'download'])
        ->name('download');

    Route::post('/{id}/cancel', [PatientHistoryController::class, 'cancel'])
        ->name('cancel');

});
