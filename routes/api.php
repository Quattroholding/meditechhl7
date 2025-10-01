<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ConsultingRoomController;
use App\Http\Controllers\Api\MedicalSpecialityController;
use App\Http\Controllers\Api\MedicationRequestController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PractitionerAuthorizationController;
use App\Http\Controllers\Api\PractitionerController;
use App\Http\Controllers\Api\Recepy\RecepyDoctorProfileController;
use App\Http\Controllers\Api\Recepy\RecepyMedicationTypeController;
use App\Http\Controllers\Api\Recepy\RecepyPrescriptionController;
use App\Http\Controllers\Api\Recepy\RecepyPrescriptionMedicationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Appointments
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('/appointments/{appointment}/availability', [AppointmentController::class, 'checkAvailability']);

    // Patient profile management
    Route::get('/patient/profile', [PatientController::class, 'profile']);
    Route::put('/patient/personal-data', [PatientController::class, 'updatePersonalData']);
    Route::put('/patient/credentials', [PatientController::class, 'updateCredentials']);
    Route::post('/patient/profile-picture', [PatientController::class, 'updateProfilePicture']);

    // Patient medical history
    Route::get('/patient/medical-history', [PatientController::class, 'medicalHistory']);
    Route::get('/patient/medical-history/{section}', [PatientController::class, 'medicalHistorySection']);

    // Medication Requests
    Route::apiResource('medication-requests', MedicationRequestController::class);

    // Medicines
    Route::apiResource('medicines', MedicineController::class);
    Route::get('/clients/{clientId}/medicines', [MedicineController::class, 'getByClient']);

    // Branches
    Route::apiResource('branches', BranchController::class);
    Route::get('/clients/{clientId}/branches', [BranchController::class, 'getByClient']);

    // Consulting Rooms
    Route::apiResource('consulting-rooms', ConsultingRoomController::class);
    Route::get('/branches/{branchId}/consulting-rooms', [ConsultingRoomController::class, 'getByBranch']);
    Route::get('/clients/{clientId}/consulting-rooms', [ConsultingRoomController::class, 'getByClient']);

    // Support resources
    Route::get('/practitioners', [PractitionerController::class, 'index']);
    Route::get('/practitioners/{practitioner}/availability', [PractitionerController::class, 'availability']);
    Route::get('/practitioners/{practitioner}/consulting-rooms', [PractitionerController::class, 'consultingRooms']);
    Route::get('/practitioners/{practitioner}/service-catalog', [PractitionerController::class, 'serviceCatalog']);
    Route::get('/medical-specialities', [MedicalSpecialityController::class, 'index']);

    // Practitioner Authorization
    Route::get('/practitioner/prescription-authorization/status', [PractitionerAuthorizationController::class, 'getPrescriptionAuthorizationStatus']);
    Route::post('/practitioner/prescription-authorization', [PractitionerAuthorizationController::class, 'updatePrescriptionAuthorization']);

    // Recepy System - Prescription Management
    Route::prefix('recepy')->group(function () {
        // Doctor Profiles
        Route::delete('doctor-profiles/delete-file', [RecepyDoctorProfileController::class, 'deleteFile']);
        Route::apiResource('doctor-profiles', RecepyDoctorProfileController::class);
        Route::get('users/{userId}/doctor-profile', [RecepyDoctorProfileController::class, 'getByUser']);

        // File Management for Doctor Profiles
        Route::post('doctor-profiles/upload-file', [RecepyDoctorProfileController::class, 'uploadFile']);

        // Prescriptions
        Route::apiResource('prescriptions', RecepyPrescriptionController::class);
        Route::patch('prescriptions/{id}/status', [RecepyPrescriptionController::class, 'updateStatus']);
        Route::get('doctor-profiles/{doctorProfileId}/prescriptions', [RecepyPrescriptionController::class, 'getByDoctorProfile']);

        // PDF Generation (original methods - use prescription's doctor profile)
        Route::get('prescriptions/{id}/pdf/download', [RecepyPrescriptionController::class, 'downloadPdf']);
        Route::get('prescriptions/{id}/pdf/stream', [RecepyPrescriptionController::class, 'streamPdf']);
        Route::get('prescriptions/{id}/pdf/url', [RecepyPrescriptionController::class, 'generatePdfUrl']);

        // PDF Generation with custom doctor profile (receives doctor_profile_id parameter)
        Route::post('prescriptions/{id}/pdf/download-with-profile', [RecepyPrescriptionController::class, 'downloadPdfWithProfile']);
        Route::post('prescriptions/{id}/pdf/stream-with-profile', [RecepyPrescriptionController::class, 'streamPdfWithProfile']);
        Route::post('prescriptions/{id}/pdf/url-with-profile', [RecepyPrescriptionController::class, 'generatePdfUrlWithProfile']);

        // Prescription Medications (nested under prescriptions)
        Route::prefix('prescriptions/{prescriptionId}/medications')->group(function () {
            Route::get('/', [RecepyPrescriptionMedicationController::class, 'index']);
            Route::post('/', [RecepyPrescriptionMedicationController::class, 'store']);
            Route::get('/{medicationId}', [RecepyPrescriptionMedicationController::class, 'show']);
            Route::put('/{medicationId}', [RecepyPrescriptionMedicationController::class, 'update']);
            Route::delete('/{medicationId}', [RecepyPrescriptionMedicationController::class, 'destroy']);
            Route::patch('/{medicationId}/toggle-active', [RecepyPrescriptionMedicationController::class, 'toggleActive']);
            Route::put('/order', [RecepyPrescriptionMedicationController::class, 'updateOrder']);
            Route::post('/bulk-update', [RecepyPrescriptionMedicationController::class, 'bulkUpdate']);
        });

        // Medication Types
        Route::get('medication-types', [RecepyMedicationTypeController::class, 'index']);
    });
});

// API Token routes - Full access with IP restrictions
Route::middleware('api.token')->prefix('v1')->group(function () {
    // All endpoints with full access
    Route::get('/practitioners', [PractitionerController::class, 'index']);
    Route::get('/practitioners/{practitioner}/availability', [PractitionerController::class, 'availability']);
    Route::get('/practitioners/{practitioner}/consulting-rooms', [PractitionerController::class, 'consultingRooms']);
    Route::get('/practitioners/{practitioner}/service-catalog', [PractitionerController::class, 'serviceCatalog']);

    // Patients
    Route::get('/patients', [PatientController::class, 'index']);
    Route::get('/patients/{patientId}/medical-history', [PatientController::class, 'getMedicalHistory']);

    // Appointments with v1-specific methods
    Route::get('/appointments', [AppointmentController::class, 'indexV1']);
    Route::post('/appointments', [AppointmentController::class, 'storeV1']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'showV1']);
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'updateV1']);
    Route::patch('/appointments/{appointment}', [AppointmentController::class, 'updateV1']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroyV1']); // Keep original destroy method
    Route::get('/appointments/{appointment}/availability', [AppointmentController::class, 'checkAvailabilityV1']);

    Route::apiResource('medicines', MedicineController::class);
    Route::get('/clients/{clientId}/medicines', [MedicineController::class, 'getByClient']);

    Route::apiResource('branches', BranchController::class);
    Route::get('/clients/{clientId}/branches', [BranchController::class, 'getByClient']);

    Route::apiResource('consulting-rooms', ConsultingRoomController::class);
    Route::get('/branches/{branchId}/consulting-rooms', [ConsultingRoomController::class, 'getByBranch']);
    Route::get('/clients/{clientId}/consulting-rooms', [ConsultingRoomController::class, 'getByClient']);

    Route::get('/medical-specialities', [MedicalSpecialityController::class, 'index']);
});
