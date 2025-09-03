<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ConsultingRoomController;
use App\Http\Controllers\Api\MedicalSpecialityController;
use App\Http\Controllers\Api\MedicationRequestController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PractitionerController;
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
    Route::get('/medical-specialities', [MedicalSpecialityController::class, 'index']);
});

// API Token routes - Full access with IP restrictions
Route::middleware('api.token')->prefix('v1')->group(function () {
    // All endpoints with full access
    Route::get('/practitioners', [PractitionerController::class, 'index']);
    Route::get('/practitioners/{practitioner}/availability', [PractitionerController::class, 'availability']);
    Route::get('/practitioners/{practitioner}/consulting-rooms', [PractitionerController::class, 'consultingRooms']);

    // Patients
    Route::get('/patients', [PatientController::class, 'index']);
    Route::get('/patients/{patientId}/medical-history', [PatientController::class, 'getMedicalHistory']);

    // Appointments with v1-specific methods
    Route::get('/appointments', [AppointmentController::class, 'indexV1']);
    Route::post('/appointments', [AppointmentController::class, 'storeV1']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'showV1']);
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'updateV1']);
    Route::patch('/appointments/{appointment}', [AppointmentController::class, 'updateV1']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']); // Keep original destroy method
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
