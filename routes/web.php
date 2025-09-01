<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FirstLoginController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MedicalDocumentController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PractitionerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Incluir el archivo de rutas de autenticación
require __DIR__.'/auth.php';

Route::get('/', function () {
    $practitioners = \App\Models\Practitioner::with(['specialties', 'files'])
        ->active()
        ->limit(12)
        ->get();

    $specialties = \App\Models\MedicalSpeciality::whereHas('practitioners', function($query) {
        $query->where('active', true);
    })->get();

    return view('welcome', compact('practitioners', 'specialties'));
})->name('welcome');

Route::get('/register', function () {
    return view('Pages.register');
})->name('patient.register');

Route::get('/login', function () {

    if (auth()->check()) {
        return redirect(route('appointment.calendar'));
    }

    return view('Pages/login');

})->name('login');

Route::get('/forgot-password', function () {
    return view('Pages/forgot-password');
})->name('forgot-password');

Route::get('/reset-password', function () {
    return view('auth/reset-password');
})->name('reset-password');

Route::get('/autologin', function () {

    if (request()->get('role') == 'admin') {
        $route = route('admin.dashboard');
    }
    if (request()->get('role') == 'admin client') {
        $route = route('client.dashboard');
    }
    if (request()->get('role') == 'doctor') {
        $route = route('doctor.dashboard');
    }
    if (request()->get('role') == 'paciente') {
        $route = route('patient.dashboard');
    }
    if (request()->get('role') == 'asistente') {
        $route = route('assistence.dashboard');
    }

    $user = \App\Models\User::role(request()->get('role'))->inRandomOrder()->limit(1)->first();

    if ($user) {
        Auth::login($user);
    }

    return redirect($route.'?show_salute=true');

})->name('autologin');

Route::get('/dash', function () {
    if (auth()->user()->hasRole('admin')) {
        $route = route('admin.dashboard');
    }
    if (auth()->user()->hasRole('admin client')) {
        $route = route('client.dashboard');
    }
    if (auth()->user()->hasRole('doctor')) {
        $route = route('doctor.dashboard');
    }
    if (auth()->user()->hasRole('paciente')) {
        $route = route('patient.dashboard');
    }

    return redirect($route);
})->name('dash')->middleware(['auth', 'verified', 'first.login']);

Route::post('/login', [LoginController::class, 'authenticate'])->name('login');

// First Login Routes
Route::middleware('auth')->group(function () {
    Route::get('/first-login', [FirstLoginController::class, 'show'])->name('first-login.show');
    Route::put('/first-login', [FirstLoginController::class, 'update'])->name('first-login.update');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/admin', [DashboardController::class, 'admin'])->middleware('permission:dashboard.admin')->name('admin.dashboard');
    Route::get('/admin-kpis', function () {
        return view('dashboard.admin-kpis');
    })->name('admin.dashboard.kpis')->middleware('role:admin');
    Route::get('/doctor', [DashboardController::class, 'doctor'])->middleware('permission:dashboard.doctor')->name('doctor.dashboard');
    Route::get('/patient', [DashboardController::class, 'patient'])->middleware('permission:dashboard.patient')->name('patient.dashboard');
    Route::get('/client', [DashboardController::class, 'admin_client'])->middleware('permission:dashboard.client')->name('client.dashboard');
    Route::get('/assistence', [DashboardController::class, 'assistence'])->middleware('permission:dashboard.assistence')->name('assistence.dashboard');

});

Route::middleware(['auth', 'first.login'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Profile Route
    Route::get('/user-profile', function () {
        return view('user-profile');
    })->name('user.profile');
});

Route::group(['prefix' => 'consultation', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [ConsultationController::class, 'index'])->middleware('permission:consultations.view')->name('consultation.index');

    Route::get('/{encounter_id}/view', [ConsultationController::class, 'view'])->middleware('permission:consultations.view')->name('consultation.view');

    Route::get('/{appointment_id}', [ConsultationController::class, 'show'])->middleware('permission:consultations.create')->name('consultation.show');

    Route::get('/{appointment_id}/download_resumen', [ConsultationController::class, 'downloadResumen'])->middleware('permission:consultations.view')->name('consultation.download_resumen');

    Route::post('/{appointment_id}', [ConsultationController::class, 'finished'])->middleware('permission:consultations.create')->name('consultation.finished');

});

// Invoice routes
Route::group(['prefix' => 'invoice', 'middleware' => ['auth', 'verified', 'first.login', 'permission:invoices.view']], function () {

    Route::get('/{invoice_id}/download', [ConsultationController::class, 'downloadInvoice'])->name('invoice.download');

});

Route::group(['prefix' => 'accounts', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::group(['prefix' => 'invoices', 'middleware' => ['auth', 'verified', 'first.login', 'permission:invoices.view']], function () {

        Route::get('/', [InvoiceController::class, 'index'])->name('invoice.index');

        Route::get('/{id}', [InvoiceController::class, 'show'])->name('invoice.show');

        Route::get('/{invoice_id}/download', [InvoiceController::class, 'download'])->name('invoice.download');

    });

    Route::group(['prefix' => 'payments', 'middleware' => ['auth', 'verified', 'first.login', 'permission:payments.view']], function () {

        Route::get('/', [PaymentController::class, 'index'])->name('payment.index');

        Route::get('/{id}', [PaymentController::class, 'show'])->name('payment.show');

        Route::get('/{invoice_id}/download', [PaymentController::class, 'download'])->name('payment.download');

    });

});

Route::post('/store_public', [PatientController::class, 'store_public'])->name('patient.public.store');

Route::group(['prefix' => 'clients', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [ClientController::class, 'index'])->middleware('permission:clients.view')->name('client.index');

    Route::get('/create', [ClientController::class, 'create'])->middleware('permission:clients.create')->name('client.create');

    Route::post('/store', [ClientController::class, 'store'])->middleware('permission:clients.create')->name('client.store');

    Route::get('/{id}/edit', [ClientController::class, 'edit'])->middleware('permission:clients.edit')->name('client.edit');

    Route::post('/{id}/update', [ClientController::class, 'update'])->middleware('permission:clients.edit')->name('client.update');

    Route::delete('/{id}/delete', [ClientController::class, 'destroy'])->middleware('permission:clients.delete')->name('client.destroy');

    Route::group(['prefix' => 'branch', 'middleware' => ['auth', 'verified', 'first.login']], function () {

        Route::get('/', [BranchController::class, 'index'])->name('client.branch.index');

        Route::get('/create', [BranchController::class, 'create'])->name('client.branch.create');

        Route::post('/store', [BranchController::class, 'store'])->name('client.branch.store');

        Route::get('/{id}/edit', [BranchController::class, 'edit'])->name('client.branch.edit');

        Route::post('/{id}/update', [BranchController::class, 'update'])->name('client.branch.update');

        Route::delete('/{id}/delete', [BranchController::class, 'destroy'])->name('client.branch.destroy');
    });

    Route::group(['prefix' => 'consulting_rooms', 'middleware' => ['auth', 'verified', 'first.login']], function () {

        Route::get('/', [RoomController::class, 'index'])->middleware('permission:rooms.view')->name('client.room.index');

        Route::get('/create', [RoomController::class, 'create'])->middleware('permission:rooms.create')->name('client.room.create');

        Route::post('/store', [RoomController::class, 'store'])->middleware('permission:rooms.create')->name('client.room.store');

        Route::get('/{id}/edit', [RoomController::class, 'edit'])->middleware('permission:rooms.edit')->name('client.room.edit');

        Route::post('/{id}/update', [RoomController::class, 'update'])->middleware('permission:rooms.edit')->name('client.room.update');

        Route::delete('/{id}/delete', [RoomController::class, 'destroy'])->middleware('permission:rooms.delete')->name('client.room.destroy');
    });

});

Route::group(['prefix' => 'packages', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [PackageController::class, 'index'])->middleware('permission:manage-packages')->name('package.index');

    Route::get('/create', [PackageController::class, 'create'])->middleware('permission:manage-packages')->name('package.create');

    Route::post('/store', [PackageController::class, 'store'])->middleware('permission:manage-packages')->name('package.store');

    Route::get('/{id}/edit', [PackageController::class, 'edit'])->middleware('permission:manage-packages')->name('package.edit');

    Route::post('/{id}/update', [PackageController::class, 'update'])->middleware('permission:manage-packages')->name('package.update');

    Route::delete('/{id}/delete', [PackageController::class, 'destroy'])->middleware('permission:manage-packages')->name('package.destroy');

});

Route::group(['prefix' => 'patients', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [PatientController::class, 'index'])->middleware('permission:patients.view')->name('patient.index');

    Route::get('/create', [PatientController::class, 'create'])->middleware('permission:patients.create')->name('patient.create');

    Route::post('/store', [PatientController::class, 'store'])->middleware('permission:patients.create')->name('patient.store');

    Route::get('/check/{id_number}', [PatientController::class, 'check'])->name('patient.check');

    Route::post('/associate', [PatientController::class, 'associate'])->middleware('permission:patients.create')->name('patient.associate');

    Route::get('/{id}/profile', [PatientController::class, 'profile'])->middleware('permission:patients.profile')->name('patient.profile');

    Route::get('/{id}/insurances', [PatientController::class, 'insurances'])->name('patient.insurances');

    Route::get('/{id}/medical_history', [PatientController::class, 'medicalHistory'])->middleware('permission:patients.medical_history')->name('patient.medical_history');

    Route::get('/{id}/edit', [PatientController::class, 'edit'])->middleware('permission:patients.edit')->name('patient.edit');

    Route::get('/{id}', [PatientController::class, 'show'])->middleware('permission:patients.edit')->name('patient.show');

    Route::post('/{id}/update', [PatientController::class, 'update'])->middleware('permission:patients.update')->name('patient.update');

    Route::delete('/{id}', [PatientController::class, 'destroy'])->middleware('permission:patients.delete')->name('patient.destroy');

});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view')->name('user.index');

    Route::get('/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('user.create');

    Route::get('/change_client/{client_id}', [UserController::class, 'changeClient'])->middleware('permission:users.change_client')->name('user.change_client');

    Route::post('/store', [UserController::class, 'store'])->middleware('permission:users.create')->name('user.store');

    Route::get('/{id}/edit', [UserController::class, 'edit'])->middleware('permission:users.edit')->name('user.edit');

    Route::post('/{id}/update', [UserController::class, 'update'])->middleware('permission:users.edit')->name('user.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('user.destroy');

    Route::post('/{id}/activate', [UserController::class, 'activate'])->middleware('permission:users.activate')->name('user.activate');

});

Route::group(['prefix' => 'appointments', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [AppointmentController::class, 'index'])->middleware('permission:appointments.view')->name('appointment.index');

    Route::get('/calendar', [AppointmentController::class, 'calendar'])->middleware('permission:appointments.view')->name('appointment.calendar');

    Route::get('/create', [AppointmentController::class, 'create'])->middleware('permission:appointments.create')->name('appointment.create');

    Route::post('/store', [AppointmentController::class, 'store'])->middleware('permission:appointments.create')->name('appointment.store');

    Route::get('/{id}/edit', [AppointmentController::class, 'edit'])->middleware('permission:appointments.edit')->name('appointment.edit');

    Route::post('/{id}/update', [AppointmentController::class, 'update'])->middleware('permission:appointments.edit')->name('appointment.update');

    Route::delete('/{id}', [AppointmentController::class, 'destroy'])->middleware('permission:appointments.delete')->name('appointment.destroy');

});

Route::group(['prefix' => 'settings', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/create_consultation_template', [SettingController::class, 'consultationTemplate'])->middleware('permission:settings.create_consultation_template')->name('setting.create_template');

    Route::get('/create_rapid_access', [SettingController::class, 'rapidAccess'])->middleware('permission:settings.create_rapid_access')->name('setting.create_rapid_access');

    Route::get('/create_cpt_user', [SettingController::class, 'cptUser'])->middleware('permission:settings.create_user_procedures')->name('setting.create_cpt_user');

    Route::get('/create_working_hour_user', [SettingController::class, 'workingHourUser'])->middleware('permission:settings.create_working_hour_user')->name('setting.create_working_hour_user');

    Route::get('/create_user_procedures', [SettingController::class, 'createUserProcedure'])->middleware('permission:settings.create_user_procedures')->name('setting.create_user_procedures');

    Route::get('/{practitioner_id}/signature_and_seal', [SettingController::class, 'uploadSignatureSeal'])->middleware('permission:settings.signature_and_seal')->name('setting.signature_and_seal');

    Route::get('/theme/{client_id}', [SettingController::class, 'themeManager'])->name('setting.theme_manager');

});

// Roles and Permissions Routes
Route::group(['prefix' => 'roles', 'middleware' => ['auth', 'verified', 'first.login', 'permission:manage-roles']], function () {
    Route::get('/', [\App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
    Route::get('/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('role.create');
    Route::post('/store', [\App\Http\Controllers\RoleController::class, 'store'])->name('role.store');
    Route::get('/{id}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->name('role.edit');
    Route::post('/{id}/update', [\App\Http\Controllers\RoleController::class, 'update'])->name('role.update');
    Route::delete('/{id}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('role.destroy');
});

Route::group(['prefix' => 'permissions', 'middleware' => ['auth', 'verified', 'first.login', 'permission:manage-permissions']], function () {
    Route::get('/', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permission.index');
    Route::get('/create', [\App\Http\Controllers\PermissionController::class, 'create'])->name('permission.create');
    Route::post('/store', [\App\Http\Controllers\PermissionController::class, 'store'])->name('permission.store');
    Route::get('/{id}/edit', [\App\Http\Controllers\PermissionController::class, 'edit'])->name('permission.edit');
    Route::post('/{id}/update', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permission.update');
    Route::delete('/{id}', [\App\Http\Controllers\PermissionController::class, 'destroy'])->name('permission.destroy');
});

Route::group(['prefix' => 'practitioners', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [PractitionerController::class, 'index'])->middleware('permission:practitioners.view')->name('practitioner.index');

    Route::get('/directory', [PractitionerController::class, 'directory'])->middleware('permission:practitioners.directory')->name('practitioner.directory');

    Route::get('/create', [PractitionerController::class, 'create'])->middleware('permission:practitioners.create')->name('practitioner.create');

    Route::post('/store', [PractitionerController::class, 'store'])->middleware('permission:practitioners.create')->name('practitioner.store');

    Route::get('/{id}/profile', [PractitionerController::class, 'profile'])->middleware('permission:practitioners.profile')->name('practitioner.profile');

    Route::get('/{id}/edit', [PractitionerController::class, 'edit'])->middleware('permission:practitioners.edit')->name('practitioner.edit');

    Route::post('/{id}/update', [PractitionerController::class, 'update'])->middleware('permission:practitioners.edit')->name('practitioner.update');

    Route::delete('/{id}', [PractitionerController::class, 'destroy'])->middleware('permission:practitioners.delete')->name('practitioner.destroy');

});

Route::group(['prefix' => 'medicines', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', function () {
        return view('medicine.index');
    })->middleware('permission:medicines.view')->name('medicine.index');

    Route::get('/create', function () {
        return view('medicine.create');
    })->middleware('permission:medicines.create')->name('medicine.create');

    Route::get('/{id}/edit', function ($id) {
        return view('medicine.edit', ['medicine_id' => $id]);
    })->middleware('permission:medicines.edit')->name('medicine.edit');

});

Route::group(['prefix' => 'service_requests', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [ServiceRequestController::class, 'index'])->middleware('permission:service_request.view')->name('service_request.index');


});

Route::group(['prefix' => 'api'], function () {

    Route::get('/diagnostics', [ApiController::class, 'diagnostics'])->name('api.diagnostics');
    Route::get('/cpts/{type}', [ApiController::class, 'cpts'])->name('api.cpts');
    Route::get('/medical_speciality', [ApiController::class, 'medicalSpeciality'])->name('api.medical_speciality');
    Route::get('/medicines', [ApiController::class, 'medicines'])->name('api.medicines');
    Route::get('/patients', [ApiController::class, 'patients'])->name('api.patients');
    Route::get('/users', [ApiController::class, 'users'])->name('api.users');
    // Route::get('/practitioners', [ApiController::class, 'practitioners'])->name('api.practitioners');
    Route::get('/services_catalog', [ApiController::class, 'servicesCatalog'])->name('api.servicesCatalog');

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

// Medical Documents Routes (PDF Generation)
Route::middleware(['auth', 'first.login'])->group(function () {

    // Prescription Routes
    Route::get('/prescription/{encounter}/download', [MedicalDocumentController::class, 'generatePrescription'])
        ->name('prescription.download');

    Route::get('/prescription/{encounter}/preview', [MedicalDocumentController::class, 'previewPrescription'])
        ->name('prescription.preview');

    Route::post('/prescription/custom/download', [MedicalDocumentController::class, 'generatePrescriptionByMedications'])
        ->name('prescription.custom.download');

    // Medical Order Routes
    Route::get('/medical-order/{encounter}/download', [MedicalDocumentController::class, 'generateMedicalOrder'])
        ->name('medical-order.download');

    Route::get('/medical-order/{encounter}/preview', [MedicalDocumentController::class, 'previewMedicalOrder'])
        ->name('medical-order.preview');

    Route::post('/medical-order/custom/download', [MedicalDocumentController::class, 'generateMedicalOrderByServices'])
        ->name('medical-order.custom.download');

    // Private File Serving Routes
    Route::get('/practitioner/{practitioner_id}/signature', [FileController::class, 'serveSignature'])
        ->name('practitioner.signature');

    Route::get('/practitioner/{practitioner_id}/seal', [FileController::class, 'serveSeal'])
        ->name('practitioner.seal');

});

// Survey Routes
Route::middleware(['auth', 'first.login', 'permission:surveys.view'])->group(function () {
    Route::resource('surveys', SurveyController::class);
});

// Public Survey Routes (no authentication required)
Route::get('/survey/{token}', [SurveyController::class, 'publicForm'])->name('survey.public');
Route::post('/survey/{token}/submit', [SurveyController::class, 'submitPublic'])->name('survey.submit');

// Insurance Companies Routes
Route::middleware(['auth', 'first.login', 'permission:manage insurances'])->group(function () {
    Route::resource('insurances', InsuranceController::class);
});

// Test route for broadcast
Route::get('/test-broadcast/{appointment_id}', function ($appointment_id) {
    $appointment = \App\Models\Appointment::find($appointment_id);
    if ($appointment) {
        broadcast(new \App\Events\AppointmentCheckedIn($appointment));

        return 'Broadcast sent for appointment '.$appointment_id.' to doctor '.$appointment->practitioner_id;
    }

    return 'Appointment not found';
})->middleware('auth')->name('test.broadcast');
