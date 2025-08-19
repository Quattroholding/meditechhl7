<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsultingRoomController;
use App\Http\Controllers\Api\MedicalSpecialityController;
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

    // Support resources
    Route::get('/practitioners', [PractitionerController::class, 'index']);
    Route::get('/practitioners/{practitioner}/availability', [PractitionerController::class, 'availability']);
    Route::get('/practitioners/{practitioner}/consulting-rooms', [PractitionerController::class, 'consultingRooms']);
    Route::get('/medical-specialities', [MedicalSpecialityController::class, 'index']);
    Route::get('/consulting-rooms', [ConsultingRoomController::class, 'index']);
});
