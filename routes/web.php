<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ClientController;
use \App\Http\Controllers\BranchController;
use \App\Http\Controllers\RoomController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PractitionerController;
use App\Http\Controllers\UserController;
use \App\Http\Controllers\AppointmentController;
use \App\Http\Controllers\ApiController;
use \App\Http\Controllers\SettingController;
use \App\Http\Controllers\Auth\LoginController;
use \App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('Pages.register');
});

Route::get('/login', function () {
    return view('Pages/login');
})->name('login');

Route::get('/forgot-password', function () {
    return view('Pages/forgot-password');
})->name('forgot-password');

Route::post('/login', [LoginController::class, 'authenticate'])->name('login');


Route::group(array('prefix' => 'dashboard','middleware'=>['auth','verified']), function() {

    Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/doctor', [DashboardController::class, 'doctor'])->name('doctor.dashboard');
    Route::get('/patient', [DashboardController::class, 'patient'])->name('patient.dashboard');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(array('prefix' => 'consultation','middleware'=>['auth','verified']), function() {

    Route::get('/', [ConsultationController::class, 'index'])->name('consultation.index');

    Route::get('/{appointment_id}', [ConsultationController::class, 'show'])->name('consultation.show');

    Route::get('/{appointment_id}/download_resumen', [ConsultationController::class, 'downloadResumen'])->name('consultation.download_resumen');

    Route::post('/{appointment_id}', [ConsultationController::class, 'finished'])->name('consultation.finished');


});

Route::post('/store_public', [PatientController::class, 'store_public'])->name('patient.public.store');

Route::group(array('prefix' => 'clients','middleware'=>['auth','verified']), function() {

    Route::get('/', [ClientController::class, 'index'])->name('client.index');

    Route::get('/create', [ClientController::class, 'create'])->name('client.create');

    Route::post('/store', [ClientController::class, 'store'])->name('client.store');

    Route::get('/{id}/edit', [ClientController::class, 'edit'])->name('client.edit');

    Route::post('/{id}/update', [ClientController::class, 'update'])->name('client.update');

    Route::delete('/{id}/delete', [ClientController::class, 'destroy'])->name('client.destroy');

    Route::group(array('prefix' => 'branch','middleware'=>['auth','verified']), function() {

        Route::get('/create', [BranchController::class, 'create'])->name('client.branch.create');

        Route::post('/store', [BranchController::class, 'store'])->name('client.branch.store');

        Route::get('/{id}/edit', [BranchController::class, 'edit'])->name('client.branch.edit');

        Route::post('/{id}/update', [BranchController::class, 'update'])->name('client.branch.update');

        Route::delete('/{id}/delete', [BranchController::class, 'destroy'])->name('client.branch.destroy');
    });

    Route::group(array('prefix' => 'consulting_rooms','middleware'=>['auth','verified']), function() {

        Route::get('/create', [RoomController::class, 'create'])->name('client.room.create');

        Route::post('/store', [RoomController::class, 'store'])->name('client.room.store');

        Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('client.room.edit');

        Route::post('/{id}/update', [RoomController::class, 'update'])->name('client.room.update');

        Route::delete('/{id}/delete', [RoomController::class, 'destroy'])->name('client.room.destroy');
    });

});

Route::group(array('prefix' => 'patients','middleware'=>['auth','verified']), function() {

    Route::get('/', [PatientController::class, 'index'])->name('patient.index');

    Route::get('/create', [PatientController::class, 'create'])->name('patient.create');

    Route::post('/store', [PatientController::class, 'store'])->name('patient.store');
    
    Route::get('/check/{id_number}', [PatientController::class, 'check'])->name('patient.check');
    
    Route::post('/associate', [PatientController::class, 'associate'])->name('patient.associate');

    Route::get('/{id}/profile', [PatientController::class, 'profile'])->name('patient.profile');

    Route::get('/{id}/edit', [PatientController::class, 'edit'])->name('patient.edit');

    Route::post('/{id}/update', [PatientController::class, 'update'])->name('patient.update');

    Route::delete('/{id}', [PatientController::class, 'destroy'])->name('patient.destroy');

});

Route::group(array('prefix' => 'users','middleware'=>['auth','verified']), function() {

    Route::get('/', [UserController::class, 'index'])->name('user.index');

    Route::get('/create', [UserController::class, 'create'])->name('user.create');

    Route::post('/store', [UserController::class, 'store'])->name('user.store');

    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit');

    Route::post('/{id}/update', [UserController::class, 'update'])->name('user.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy');

});

Route::group(array('prefix' => 'appointments','middleware'=>['auth','verified']), function() {

    Route::get('/', [AppointmentController::class, 'index'])->name('appointment.index');

    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('appointment.calendar');

    Route::get('/create', [AppointmentController::class, 'create'])->name('appointment.create');

    Route::post('/store', [AppointmentController::class, 'store'])->name('appointment.store');

    Route::get('/{id}/edit', [AppointmentController::class, 'edit'])->name('appointment.edit');

    Route::post('/{id}/update', [AppointmentController::class, 'update'])->name('appointment.update');

    Route::delete('/{id}', [AppointmentController::class, 'destroy'])->name('appointment.destroy');

});

Route::group(array('prefix' => 'settings','middleware'=>['auth','verified']), function() {

    Route::get('/create_consultation_template', [SettingController::class, 'consultationTemplate'])->name('setting.create_template');

    Route::get('/create_rapid_access', [SettingController::class, 'rapidAccess'])->name('setting.create_rapid_access');

    Route::get('/create_cpt_user', [SettingController::class, 'cptUser'])->name('setting.create_cpt_user');

    Route::get('/create_working_hour_user', [SettingController::class, 'workingHourUser'])->name('setting.create_working_hour_user');

    Route::get('/create_user_procedures', [SettingController::class, 'createUserProcedure'])->name('setting.create_user_procedures');

});

Route::group(array('prefix' => 'practitioners','middleware'=>['auth','verified']), function() {

    Route::get('/', [PractitionerController::class, 'index'])->name('practitioner.index');

    Route::get('/create', [PractitionerController::class, 'create'])->name('practitioner.create');

    Route::post('/store', [PractitionerController::class, 'store'])->name('practitioner.store');

    Route::get('/{id}/profile', [PractitionerController::class, 'profile'])->name('practitioner.profile');

    Route::get('/{id}/edit', [PractitionerController::class, 'edit'])->name('practitioner.edit');

    Route::post('/{id}/update', [PractitionerController::class, 'update'])->name('practitioner.update');

    Route::delete('/{id}', [PractitionerController::class, 'destroy'])->name('practitioner.destroy');

});

Route::group(array('prefix' => 'api'), function() {

    Route::get('/diagnostics', [ApiController::class, 'diagnostics'])->name('api.diagnostics');
    Route::get('/cpts/{type}', [ApiController::class, 'cpts'])->name('api.cpts');
    Route::get('/medical_speciality', [ApiController::class, 'medicalSpeciality'])->name('api.medical_speciality');
    Route::get('/medicines', [ApiController::class, 'medicines'])->name('api.medicines');
    Route::get('/patients', [ApiController::class, 'patients'])->name('api.patients');
    Route::get('/users', [ApiController::class, 'users'])->name('api.users');
    Route::get('/practitioners', [ApiController::class, 'practitioners'])->name('api.practitioners');

});


