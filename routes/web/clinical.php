<?php

/*
|--------------------------------------------------------------------------
| Clinical Operations Routes
|--------------------------------------------------------------------------
|
| Propósito: Todas las operaciones clínicas del sistema incluyendo consultas,
|            citas médicas, documentación médica (recetas, órdenes, referencias,
|            reposos), solicitudes de servicios, medicamentos, aseguradoras,
|            encuestas de satisfacción, y dermatología.
|
| Middleware Común: ['auth', 'verified', 'first.login'] o ['auth', 'first.login']
|
| Controladores Principales:
| - ConsultationController: Gestión de consultas y encuentros médicos
| - AppointmentController: Gestión de citas médicas y calendario
| - MedicalDocumentController: Generación de PDFs médicos (recetas, órdenes, referencias, reposos)
| - ServiceRequestController: Gestión de solicitudes de servicios (laboratorio, imagen, etc.)
| - MedicineController: Catálogo de medicamentos
| - InsuranceController: Gestión de aseguradoras
| - SurveyController: Encuestas de satisfacción del paciente
| - DermatologyController: Seguimiento dermatológico de pacientes
|
| Prefijos de Rutas:
| - /consultation: Consultas médicas
| - /appointments: Citas médicas
| - /dermatology: Seguimiento dermatológico
| - /prescription, /medical-order, /referral-order, /medical-leaves: Documentos médicos PDF
| - /service_requests: Solicitudes de servicios
| - /medicines: Catálogo de medicamentos
| - /insurances: Aseguradoras
| - /surveys: Encuestas de satisfacción
|
*/

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DermatologyController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\MedicalDocumentController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// CONSULTATION ROUTES - Gestión de Consultas y Encuentros Médicos
// ============================================================================

Route::group(['prefix' => 'consultation', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    // Lista de consultas/encuentros
    Route::get('/', [ConsultationController::class, 'index'])
        ->middleware('permission:consultations.view')
        ->name('consultation.index');

    // Ver consulta finalizada (encounter_id)
    Route::get('/{encounter_id}/view', [ConsultationController::class, 'view'])
        ->middleware('permission:consultations.show')
        ->name('consultation.view');

    // Reenviar recetas por WhatsApp
    Route::post('/{encounter_id}/resend-whatsapp', [ConsultationController::class, 'resendPrescriptionsWhatsApp'])
        ->middleware('permission:consultations.view')
        ->name('consultation.resend-whatsapp');

    // Mostrar formulario de consulta (appointment_id)
    Route::get('/{appointment_id}', [ConsultationController::class, 'show'])
        ->middleware('permission:consultations.create')
        ->name('consultation.show');

    // Descargar resumen de consulta
    Route::get('/{appointment_id}/download_resumen', [ConsultationController::class, 'downloadResumen'])
        ->middleware('permission:consultations.download_resumen')
        ->name('consultation.download_resumen');

    // Finalizar consulta
    Route::post('/{appointment_id}', [ConsultationController::class, 'finished'])
        ->middleware('permission:consultations.create')
        ->name('consultation.finished');

});

// ============================================================================
// APPOINTMENT ROUTES - Gestión de Citas Médicas y Calendario
// ============================================================================

Route::group(['prefix' => 'appointments', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    // Lista de citas
    Route::get('/', [AppointmentController::class, 'index'])
        ->middleware('permission:appointments.view')
        ->name('appointment.index');

    // Vista de calendario
    Route::get('/calendar', [AppointmentController::class, 'calendar'])
        ->middleware('permission:appointments.view')
        ->name('appointment.calendar');

    // Crear nueva cita
    Route::get('/create', [AppointmentController::class, 'create'])
        ->middleware('permission:appointments.create')
        ->name('appointment.create');

    // Guardar nueva cita
    Route::post('/store', [AppointmentController::class, 'store'])
        ->middleware('permission:appointments.create')
        ->name('appointment.store');

    // Editar cita
    Route::get('/{id}/edit', [AppointmentController::class, 'edit'])
        ->middleware('permission:appointments.edit')
        ->name('appointment.edit');

    // Actualizar cita
    Route::post('/{id}/update', [AppointmentController::class, 'update'])
        ->middleware('permission:appointments.edit')
        ->name('appointment.update');

    // Eliminar cita
    Route::delete('/{id}', [AppointmentController::class, 'destroy'])
        ->middleware('permission:appointments.delete')
        ->name('appointment.destroy');

});

// ============================================================================
// DERMATOLOGY ROUTES - Seguimiento Dermatológico Independiente
// ============================================================================

Route::group(['prefix' => 'dermatology', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    // Lista de pacientes con seguimiento dermatológico
    Route::get('/', [DermatologyController::class, 'index'])
        ->name('dermatology.index');

    // Ver seguimiento dermatológico de un paciente
    Route::get('/patient/{patient}', [DermatologyController::class, 'show'])
        ->name('dermatology.show');

});

// ============================================================================
// MEDICAL DOCUMENT ROUTES - Generación de Documentos Médicos PDF
// ============================================================================

Route::middleware(['auth', 'first.login'])->group(function () {

    // --------------------------------------------------------------------
    // PRESCRIPTION ROUTES - Recetas Médicas
    // --------------------------------------------------------------------

    // Descargar receta médica de un encuentro
    Route::get('/prescription/{encounter}/download', [MedicalDocumentController::class, 'generatePrescription'])
        ->name('prescription.download');

    // Previsualizar receta médica
    Route::get('/prescription/{encounter}/preview', [MedicalDocumentController::class, 'previewPrescription'])
        ->name('prescription.preview');

    // Generar receta personalizada (lista de medicamentos custom)
    Route::post('/prescription/custom/download', [MedicalDocumentController::class, 'generatePrescriptionByMedications'])
        ->name('prescription.custom.download');

    // --------------------------------------------------------------------
    // MEDICAL ORDER ROUTES - Órdenes Médicas (Laboratorio, Imagen, etc.)
    // --------------------------------------------------------------------

    // Descargar orden médica de un encuentro
    Route::get('/medical-order/{encounter}/download', [MedicalDocumentController::class, 'generateMedicalOrder'])
        ->name('medical-order.download');

    // Previsualizar orden médica
    Route::get('/medical-order/{encounter}/preview', [MedicalDocumentController::class, 'previewMedicalOrder'])
        ->name('medical-order.preview');

    // Generar orden médica personalizada (lista de servicios custom)
    Route::post('/medical-order/custom/download', [MedicalDocumentController::class, 'generateMedicalOrderByServices'])
        ->name('medical-order.custom.download');

    // --------------------------------------------------------------------
    // REFERRAL ORDER ROUTES - Órdenes de Referencia (Interconsultas)
    // --------------------------------------------------------------------

    // Descargar orden de referencia
    Route::get('/referral-order/{encounter}/download', [MedicalDocumentController::class, 'generateReferralOrder'])
        ->name('referral-order.download');

    // --------------------------------------------------------------------
    // MEDICAL LEAVE ROUTES - Reposos Médicos
    // --------------------------------------------------------------------

    // Descargar reposo médico
    Route::get('/medical-leaves/{id}/download', [MedicalDocumentController::class, 'downloadMedicalLeavePdf'])
        ->name('medical-leaves.download-pdf');

});

// ============================================================================
// SERVICE REQUEST ROUTES - Solicitudes de Servicios (Laboratorio, Imagen, etc.)
// ============================================================================

Route::group(['prefix' => 'service_requests', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    // Lista de solicitudes de servicios
    Route::get('/', [ServiceRequestController::class, 'index'])
        ->middleware('permission:service_request.view')
        ->name('service_request.index');

});

// ============================================================================
// MEDICINE ROUTES - Catálogo de Medicamentos
// ============================================================================

Route::group(['prefix' => 'medicines', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    // Lista de medicamentos
    Route::get('/', [MedicineController::class, 'index'])
        ->middleware('permission:medicines.view')
        ->name('medicine.index');

    // Crear nuevo medicamento
    Route::get('/create', [MedicineController::class, 'create'])
        ->middleware('permission:medicines.create')
        ->name('medicine.create');

    // Editar medicamento
    Route::get('/{id}/edit', [MedicineController::class, 'edit'])
        ->middleware('permission:medicines.edit')
        ->name('medicine.edit');

});

// ============================================================================
// INSURANCE ROUTES - Gestión de Aseguradoras
// ============================================================================

// Insurance Companies Routes (Resource Routes)
Route::middleware(['auth', 'first.login', 'permission:manage insurances'])->group(function () {
    Route::resource('insurances', InsuranceController::class);
});

// ============================================================================
// SURVEY ROUTES - Encuestas de Satisfacción del Paciente
// ============================================================================

Route::middleware(['auth', 'first.login'])->group(function () {

    // --------------------------------------------------------------------
    // VIEW ROUTES - Ver encuestas (index, show)
    // --------------------------------------------------------------------

    Route::get('surveys', [SurveyController::class, 'index'])
        ->middleware('permission:surveys.view')
        ->name('surveys.index');

    Route::get('surveys/{survey}', [SurveyController::class, 'show'])
        ->middleware('permission:surveys.view')
        ->name('surveys.show');

    // --------------------------------------------------------------------
    // CREATE ROUTES - Crear encuestas (create, store)
    // --------------------------------------------------------------------

    Route::get('surveys/create', [SurveyController::class, 'create'])
        ->middleware('permission:surveys.create')
        ->name('surveys.create');

    Route::post('surveys', [SurveyController::class, 'store'])
        ->middleware('permission:surveys.create')
        ->name('surveys.store');

    // --------------------------------------------------------------------
    // EDIT ROUTES - Editar encuestas (edit, update)
    // --------------------------------------------------------------------

    Route::get('surveys/{survey}/edit', [SurveyController::class, 'edit'])
        ->middleware('permission:surveys.edit')
        ->name('surveys.edit');

    Route::put('surveys/{survey}', [SurveyController::class, 'update'])
        ->middleware('permission:surveys.edit')
        ->name('surveys.update');

    Route::patch('surveys/{survey}', [SurveyController::class, 'update'])
        ->middleware('permission:surveys.edit');

    // --------------------------------------------------------------------
    // DELETE ROUTE - Eliminar encuestas (destroy)
    // --------------------------------------------------------------------

    Route::delete('surveys/{survey}', [SurveyController::class, 'destroy'])
        ->middleware('permission:surveys.delete')
        ->name('surveys.destroy');

});

// ============================================================================
// PUBLIC SURVEY ROUTES - Encuestas Públicas (Sin Autenticación)
// ============================================================================

// Formulario público de encuesta (acceso por token)
Route::get('/survey/{token}', [SurveyController::class, 'publicForm'])
    ->name('survey.public');

// Envío de encuesta pública
Route::post('/survey/{token}/submit', [SurveyController::class, 'submitPublic'])
    ->name('survey.submit');
