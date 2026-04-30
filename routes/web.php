<?php

use App\Events\AppointmentCheckedIn;
use App\Http\Controllers\Api\Recepy\RecepyDoctorProfileController;
use App\Http\Controllers\Api\Recepy\RecepyPrescriptionController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\AppointmentActionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorLoginController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugLoginController;
use App\Http\Controllers\DermatologyController;
use App\Http\Controllers\EnterpriseLeadController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FirstLoginController;
use App\Http\Controllers\HemoScreenExportController;
use App\Http\Controllers\HemoScreenStandaloneWebController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MedicalDocumentController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PractitionerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPatientRegistrationController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReferralCodeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SuscriptionController;
use App\Http\Controllers\SuscriptionInvoiceController;
use App\Http\Controllers\SuscriptionPaymentController;
use App\Http\Controllers\TwoFactorAdminController;
use App\Http\Controllers\TwoFactorEmailBackupController;
use App\Http\Controllers\UserController;
use App\Livewire\User\Profile;
use App\Models\Appointment;
use App\Models\Rol;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

// Incluir el archivo de rutas de autenticación
require __DIR__.'/auth.php';

// Rutas para el subdominio SAMI (funciona con cualquier dominio base)
Route::domain('sami.{domain}')->where(['domain' => '.*'])->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('sami.home');
    Route::get('/recetas', [LandingController::class, 'recetas'])->name('sami.recetas');
    Route::get('/api/practitioners', [LandingController::class, 'getPractitioners'])->name('sami.api.practitioners');
});

// Rutas para el subdominio SAMIRX (funciona con cualquier dominio base)
Route::domain('hemoscreen.{domain}')->where(['domain' => '.*'])->group(function () {
    Route::get('/', [LandingController::class, 'hemoscreen'])->name('hemoscreen.landing');
});

Route::domain('samirx.{domain}')->where(['domain' => '.*'])->group(function () {
    Route::get('/', [LandingController::class, 'recetas'])->name('samirx.home');
});

Route::get('/', [LandingController::class, 'welcome'])->name('welcome');
Route::get('/sami', [LandingController::class, 'index'])->name('sami');
Route::get('/sami_recetas', [LandingController::class, 'recetas'])->name('sami_recetas');
Route::get('/api/practitioners', [LandingController::class, 'getPractitioners'])->name('api.practitioners');

Route::get('/pacientes', [LandingController::class, 'patientLanding'])->name('patients.landing');

/*
Route::get('/register', function () {
    return view('Pages.register');
})->name('patient.register');
*/

// Public Legal Pages
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy.policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms.service');

// Registro Público de Clientes
Route::get('/register/client', [PublicRegistrationController::class, 'showForm'])
    ->name('public.register');
Route::post('/register/client', [PublicRegistrationController::class, 'store'])
    ->middleware('throttle:5,60')
    ->name('public.register.store');
Route::get('/register/success', [PublicRegistrationController::class, 'success'])
    ->name('public.register.success');

// Registro Público de Pacientes
// IMPORTANTE: La ruta de success debe ir ANTES de la ruta con {client} para evitar conflictos
Route::get('/register/patient/success', [PublicPatientRegistrationController::class, 'success'])
    ->name('public.patient.register.success');

Route::get('/register/patient/{client}', [PublicPatientRegistrationController::class, 'showForm'])
    ->name('public.patient.register');
Route::post('/register/patient/{client}', [PublicPatientRegistrationController::class, 'store'])
    ->middleware('throttle:15,60')
    ->name('public.patient.register.store');

// Enterprise Leads (Público)
Route::post('/enterprise-contact', [EnterpriseLeadController::class, 'store'])
    ->middleware('throttle:enterprise-leads')
    ->name('enterprise.lead.store');

// Enterprise Leads (Admin - Protegido)
Route::middleware(['auth', 'verified'])->prefix('admin/leads')->group(function () {
    Route::get('/', [EnterpriseLeadController::class, 'index'])->name('leads.index');
    Route::get('/{id}', [EnterpriseLeadController::class, 'show'])->name('leads.show');
    Route::post('/{id}/update', [EnterpriseLeadController::class, 'update'])->name('leads.update');
    Route::post('/{id}/convert', [EnterpriseLeadController::class, 'convertToClient'])->name('leads.convert');
});

// Referral Code PDF Download (Protegido)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/referral-code/{client}/pdf', [ReferralCodeController::class, 'downloadPdf'])
        ->name('referral.code.pdf');
});

Route::get('/login', [LoginController::class, 'login'])->name('login');

Route::get('/forgot-password', function () {
    return view('Pages/forgot-password');
})->name('forgot-password');

Route::get('/reset-password', function () {
    return view('auth/reset-password');
})->name('reset-password');

/*
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
    if (request()->get('role') == 'asistente' or request()->get('role') == 'recepcionista') {
        $route = route('assistence.dashboard');
    }
    if (request()->get('role') == 'contabilidad') {
        $route = route('accounting.dashboard');
    }

    $user = \App\Models\User::role(request()->get('role'))->whereActive(1)->inRandomOrder()->limit(1)->first();

    if ($user) {
        Auth::login($user);
    }

    return redirect($route.'?show_salute=true');

})->name('autologin');
*/

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
    if (auth()->user()->hasRole('recepcionista') or auth()->user()->hasRole('asistente medico')) {
        $route = route('assistence.dashboard');
    }
    if (auth()->user()->hasRole('contabilidad')) {
        $route = route('accounting.dashboard');
    }
    if (auth()->user()->hasRole('hemoscreen')) {
        $route = route('hemoscreen.dashboard');
    }

    return redirect($route);
})->name('dash')->middleware(['auth', 'verified', 'first.login']);

Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');

Route::get('/login/concurrent-session', [LoginController::class, 'showConcurrentSession'])->name('login.concurrent-session');

Route::post('/login/cancel', [LoginController::class, 'cancelLogin'])->name('login.cancel');

// Two-Factor Authentication Challenge Routes (before auth - user not authenticated yet)
Route::get('/two-factor-challenge', [TwoFactorLoginController::class, 'show'])
    ->name('two-factor.login');
Route::post('/two-factor-challenge', [TwoFactorLoginController::class, 'verify'])
    ->name('two-factor.verify');

// First Login Routes
Route::middleware('auth')->group(function () {
    Route::get('/first-login', [FirstLoginController::class, 'show'])->name('first-login.show');
    Route::put('/first-login', [FirstLoginController::class, 'update'])->name('first-login.update');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/admin', [DashboardController::class, 'admin'])->middleware('permission:dashboard.admin')->name('admin.dashboard');
    Route::get('/admin-kpis', function () {
        return view('Dashboard.admin-kpis');
    })->name('admin.dashboard.kpis')->middleware('role:admin');
    Route::get('/doctor', [DashboardController::class, 'doctor'])->middleware('permission:dashboard.doctor')->name('doctor.dashboard');
    Route::get('/patient', [DashboardController::class, 'patient'])->middleware('permission:dashboard.patient')->name('patient.dashboard');
    Route::get('/client', [DashboardController::class, 'admin_client'])->middleware('permission:dashboard.client')->name('client.dashboard');
    Route::get('/recepcionist', [DashboardController::class, 'assistence'])->middleware('permission:dashboard.assistence')->name('assistence.dashboard');
    Route::get('/accounting', [DashboardController::class, 'accounting'])->middleware('permission:dashboard.accounting')->name('accounting.dashboard');

});

Route::middleware(['auth', 'first.login'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Profile Route
    Route::get('/user-profile', Profile::class)->name('user.profile');

    // Two-Factor Settings Page (Dedicated - avoids nested component issues)
    Route::get('/two-factor-settings', function () {
        return view('two-factor-settings');
    })->name('two-factor.settings');

    // Notifications Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    // Ticket System Routes
    Route::get('/tickets', function () {
        return view('ticket-system.index');
    })->name('tickets.index')->middleware('permission:tickets.index');
});

Route::group(['prefix' => 'consultation', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [ConsultationController::class, 'index'])->middleware('permission:consultations.view')->name('consultation.index');

    Route::get('/{encounter_id}/view', [ConsultationController::class, 'view'])->middleware('permission:consultations.show')->name('consultation.view');

    Route::post('/{encounter_id}/resend-whatsapp', [ConsultationController::class, 'resendPrescriptionsWhatsApp'])->middleware('permission:consultations.view')->name('consultation.resend-whatsapp');

    Route::get('/{appointment_id}', [ConsultationController::class, 'show'])->middleware('permission:consultations.create')->name('consultation.show');

    Route::get('/{appointment_id}/download_resumen', [ConsultationController::class, 'downloadResumen'])->middleware('permission:consultations.download_resumen')->name('consultation.download_resumen');

    Route::post('/{appointment_id}', [ConsultationController::class, 'finished'])->middleware('permission:consultations.create')->name('consultation.finished');

});

// Dermatology routes - Independent patient tracking
Route::group(['prefix' => 'dermatology', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [DermatologyController::class, 'index'])->name('dermatology.index');

    Route::get('/patient/{patient}', [DermatologyController::class, 'show'])->name('dermatology.show');

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

    Route::get('/referral_code', [ClientController::class, 'getReferralCode'])->middleware('can.manage.subscription')->name('client.referral_code');

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

    Route::put('/{id}/update', [PatientController::class, 'update'])->middleware('permission:patients.update')->name('patient.update');

    Route::delete('/{id}', [PatientController::class, 'destroy'])->middleware('permission:patients.delete')->name('patient.destroy');

});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view')->name('user.index');

    Route::get('/create', [UserController::class, 'create'])->middleware(['permission:users.create', 'can.manage.subscription'])->name('user.create');

    Route::get('/change_client/{client_id}', [UserController::class, 'changeClient'])->middleware('permission:users.change_client')->name('user.change_client');

    Route::post('/store', [UserController::class, 'store'])->middleware(['permission:users.create', 'can.manage.subscription'])->name('user.store');

    Route::get('/{id}/edit', [UserController::class, 'edit'])->middleware('permission:users.edit')->name('user.edit');

    Route::post('/{id}/update', [UserController::class, 'update'])->middleware('permission:users.edit')->name('user.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('user.destroy');

    Route::post('/{id}/activate', [UserController::class, 'activate'])->middleware('permission:users.activate')->name('user.activate');

    Route::get('/pending-validations', function () {
        return view('users.pending-validations');
    })->middleware('permission:users.validate')->name('user.pending-validations');

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

    Route::get('/invoice-template', [SettingController::class, 'invoiceTemplate'])->middleware('permission:settings.invoice_template')->name('setting.invoice_template');
    Route::get('/invoice-template/preview/{template}', [SettingController::class, 'invoiceTemplatePreview'])->middleware('permission:settings.invoice_template')->name('setting.invoice_template.preview');

    Route::get('/medical-leave-template', [SettingController::class, 'medicalLeaveTemplate'])->middleware('can.manage.subscription')->name('setting.medical_leave_template');
    Route::get('/medical-leave-template/preview/{template}', [SettingController::class, 'medicalLeaveTemplatePreview'])->name('setting.medical_leave_template.preview');

    Route::get('/prescription-template', [SettingController::class, 'prescriptionTemplate'])->middleware('can.manage.subscription')->name('setting.prescription_template');
    Route::get('/prescription-template/preview/{template}', [SettingController::class, 'prescriptionTemplatePreview'])->name('setting.prescription_template.preview');

});

// Roles and Permissions Routes
Route::group(['prefix' => 'roles', 'middleware' => ['auth', 'verified', 'first.login', 'permission:manage-roles']], function () {
    Route::get('/', [RoleController::class, 'index'])->name('role.index');
    Route::get('/create', [RoleController::class, 'create'])->name('role.create');
    Route::post('/store', [RoleController::class, 'store'])->name('role.store');
    Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('/{id}/update', [RoleController::class, 'update'])->name('role.update');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('role.destroy');
});

Route::group(['prefix' => 'permissions', 'middleware' => ['auth', 'verified', 'first.login', 'permission:manage-permissions']], function () {
    Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
    Route::get('/create', [PermissionController::class, 'create'])->name('permission.create');
    Route::post('/store', [PermissionController::class, 'store'])->name('permission.store');
    Route::get('/{id}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
    Route::post('/{id}/update', [PermissionController::class, 'update'])->name('permission.update');
    Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('permission.destroy');
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

    Route::get('/', [MedicineController::class, 'index'])
        ->middleware('permission:medicines.view')
        ->name('medicine.index');

    Route::get('/create', [MedicineController::class, 'create'])
        ->middleware('permission:medicines.create')
        ->name('medicine.create');

    Route::get('/{id}/edit', [MedicineController::class, 'edit'])
        ->middleware('permission:medicines.edit')
        ->name('medicine.edit');

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

// Medical Documents Routes (PDF Generation)
Route::middleware(['auth', 'first.login'])->group(function () {

    // Web Prescription PDF Download (authenticated via session)
    Route::get('/prescriptions/{id}/download', [RecepyPrescriptionController::class, 'downloadPdfWeb'])->name('prescription.web.download');

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

    // Referral Order Routes
    Route::get('/referral-order/{encounter}/download', [MedicalDocumentController::class, 'generateReferralOrder'])
        ->name('referral-order.download');

    // Medical Leave Routes
    Route::get('/medical-leaves/{id}/download', [MedicalDocumentController::class, 'downloadMedicalLeavePdf'])
        ->name('medical-leaves.download-pdf');

    // Private File Serving Routes
    Route::get('/practitioner/{practitioner_id}/signature', [FileController::class, 'serveSignature'])
        ->name('practitioner.signature');

    Route::get('/practitioner/{practitioner_id}/seal', [FileController::class, 'serveSeal'])
        ->name('practitioner.seal');

    // Recepy Doctor Profile Private File Serving Routes
    Route::get('/recepy/doctor/file/{type}/{filename}', [RecepyDoctorProfileController::class, 'serveFile'])
        ->where('type', 'signature|seal')
        ->name('recepy.doctor.file');

    // Recepy Prescription Private PDF Serving Route
    Route::get('/recepy/prescription/pdf/{filename}', [RecepyPrescriptionController::class, 'servePdf'])
        ->name('recepy.prescription.pdf');

});

// Cotizaciones Routes
Route::middleware(['auth', 'first.login'])->prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [QuotationController::class, 'index'])->middleware('permission:quotations.view')->name('index');
    Route::get('/{id}/pdf', [QuotationController::class, 'streamPdf'])->middleware('permission:quotations.pdf')->name('pdf.stream');
    Route::get('/{id}/download', [QuotationController::class, 'downloadPdf'])->middleware('permission:quotations.pdf')->name('pdf.download');
    Route::post('/{id}/status', [QuotationController::class, 'changeStatus'])
        ->middleware('permission:quotations.edit')->name('status');
});

// Tipos de Servicio Routes
Route::middleware(['auth', 'first.login', 'permission:service_types.view'])->prefix('service-types')->name('service-types.')->group(function () {
    Route::get('/', [QuotationController::class, 'serviceTypes'])->name('index');
});

// Public Prescription PDF Download with Token Authentication
Route::get('prescriptions/{id}/pdf/download', [RecepyPrescriptionController::class, 'downloadPdf']);

// Survey Routes
Route::middleware(['auth', 'first.login'])->group(function () {
    // View routes (index, show)
    Route::get('surveys', [SurveyController::class, 'index'])
        ->middleware('permission:surveys.view')
        ->name('surveys.index');
    Route::get('surveys/{survey}', [SurveyController::class, 'show'])
        ->middleware('permission:surveys.view')
        ->name('surveys.show');

    // Create routes (create, store)
    Route::get('surveys/create', [SurveyController::class, 'create'])
        ->middleware('permission:surveys.create')
        ->name('surveys.create');
    Route::post('surveys', [SurveyController::class, 'store'])
        ->middleware('permission:surveys.create')
        ->name('surveys.store');

    // Edit routes (edit, update)
    Route::get('surveys/{survey}/edit', [SurveyController::class, 'edit'])
        ->middleware('permission:surveys.edit')
        ->name('surveys.edit');
    Route::put('surveys/{survey}', [SurveyController::class, 'update'])
        ->middleware('permission:surveys.edit')
        ->name('surveys.update');
    Route::patch('surveys/{survey}', [SurveyController::class, 'update'])
        ->middleware('permission:surveys.edit');

    // Delete route (destroy)
    Route::delete('surveys/{survey}', [SurveyController::class, 'destroy'])
        ->middleware('permission:surveys.delete')
        ->name('surveys.destroy');
});

// Public Survey Routes (no authentication required)
Route::get('/survey/{token}', [SurveyController::class, 'publicForm'])->name('survey.public');
Route::post('/survey/{token}/submit', [SurveyController::class, 'submitPublic'])->name('survey.submit');

// Insurance Companies Routes
Route::middleware(['auth', 'first.login', 'permission:manage insurances'])->group(function () {
    Route::resource('insurances', InsuranceController::class);
});

// API Tokens Routes (Admin only)
Route::middleware(['auth', 'first.login', 'role:admin'])->group(function () {
    Route::resource('api-tokens', ApiTokenController::class);
    // Route::get('/api-tokens/create', [ApiTokenController::class, 'create'])->name('api-tokens.create');
    // Route::get('/api-tokens/{apiToken}/edit', [ApiTokenController::class, 'edit'])->name('api-tokens.edit');
    Route::post('/api-tokens/{apiToken}/toggle', [ApiTokenController::class, 'toggle'])->name('api-tokens.toggle');
    Route::post('/api-tokens/{apiToken}/regenerate', [ApiTokenController::class, 'regenerate'])->name('api-tokens.regenerate');
});

// Test route for broadcast
Route::get('/test-broadcast/{appointment_id}', function ($appointment_id) {
    $appointment = Appointment::find($appointment_id);
    if ($appointment) {
        broadcast(new AppointmentCheckedIn($appointment));

        return 'Broadcast sent for appointment '.$appointment_id.' to doctor '.$appointment->practitioner_id;
    }

    return 'Appointment not found';
})->middleware('auth')->name('test.broadcast');

// Appointment WhatsApp Actions (Public routes with token validation)
Route::prefix('appointment-action')->name('appointment.action.')->group(function () {
    Route::get('/{appointmentId}/confirm/{token}', [AppointmentActionController::class, 'confirm'])->name('confirm');
    Route::get('/{appointmentId}/cancel/{token}', [AppointmentActionController::class, 'cancel'])->name('cancel');
});

// Appointment Email Actions (Public routes with signed URL validation for practitioners)
Route::prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/{appointment}/confirm', [AppointmentActionController::class, 'confirmSigned'])->name('confirm');
    Route::get('/{appointment}/cancel', [AppointmentActionController::class, 'cancelSigned'])->name('cancel');
});

// Virtual Consultation Room - Public Patient Access (with token)
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

Route::middleware(['auth', 'first.login'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/{report}', [ReportController::class, 'show'])->name('show');
    Route::post('/{report}/excel', [ReportController::class, 'excel'])->name('excel');
    Route::post('/{report}/pdf', [ReportController::class, 'pdf'])->name('pdf');
});

Route::group(['prefix' => 'suscriptions', 'middleware' => ['auth', 'verified', 'first.login', 'can.manage.subscription']], function () {

    // Lista de todas las suscripciones (solo administradores)
    Route::get('/list', [SuscriptionController::class, 'index'])->middleware('permission:suscriptions.manage')->name('suscriptions.index');

    Route::get('/', [SuscriptionController::class, 'show'])->middleware('permission:suscriptions.show')->name('suscriptions.show');

    Route::get('/edit/{subscription}', [SuscriptionController::class, 'edit'])->middleware('permission:suscriptions.manage')->name('suscriptions.edit');

    Route::get('/upgrade', [SuscriptionController::class, 'upgrade'])->middleware('permission:suscriptions.show')->name('suscriptions.upgrade');

    Route::post('/upgrade', [SuscriptionController::class, 'processUpgrade'])->middleware('permission:suscriptions.show')->name('suscriptions.upgrade.process');

    Route::group(['prefix' => 'invoices', 'middleware' => ['auth', 'verified', 'first.login']], function () {

        Route::get('/', [SuscriptionInvoiceController::class, 'index'])->middleware('permission:suscriptions.invoices.index')->name('suscriptions.invoices.index');

        Route::get('/pending', [SuscriptionInvoiceController::class, 'pending'])->middleware('permission:suscriptions.invoices.pending')->name('suscriptions.invoices.pending');

        Route::get('/{id}', [SuscriptionInvoiceController::class, 'show'])->middleware('permission:suscriptions.invoices.show')->name('suscriptions.invoices.show');

        Route::get('/{id}/download', [SuscriptionInvoiceController::class, 'download'])->middleware('permission:suscriptions.invoices.download')->name('suscriptions.invoices.download');

        Route::get('/{id}/edit', [SuscriptionInvoiceController::class, 'edit'])->middleware('permission:suscriptions.invoices.edit')->name('suscriptions.invoices.edit');

        Route::delete('/{id}/delete', [SuscriptionInvoiceController::class, 'destroy'])->middleware('permission:suscriptions.invoices.destroy')->name('suscriptions.invoices.destroy');
    });

    Route::group(['prefix' => 'payments', 'middleware' => ['auth', 'verified', 'first.login']], function () {

        Route::get('/', [SuscriptionPaymentController::class, 'index'])->middleware('permission:suscriptions.payments.index')->name('suscriptions.payments.index');

        Route::get('/settings', [SuscriptionPaymentController::class, 'settings'])->middleware('permission:suscriptions.payments.settings')->name('suscriptions.payments.settings');

        Route::get('/{id}/verify', [SuscriptionPaymentController::class, 'verify'])->middleware('permission:suscriptions.payments.verify')->name('suscriptions.payments.verify');

        Route::post('/{id}/reject', [SuscriptionPaymentController::class, 'reject'])->middleware('permission:suscriptions.payments.verify')->name('suscriptions.payments.reject');

        Route::get('/{id}/download-receipt', [SuscriptionPaymentController::class, 'downloadReceipt'])->middleware('permission:suscriptions.payments.show')->name('suscriptions.payments.download-receipt');

        Route::get('/{id}', [SuscriptionPaymentController::class, 'show'])->middleware('permission:suscriptions.payments.show')->name('suscriptions.payments.show');

        Route::get('/{id}/download', [SuscriptionPaymentController::class, 'download'])->middleware('permission:suscriptions.payments.download')->name('suscriptions.payments.download');

        Route::get('/{id}/edit', [SuscriptionPaymentController::class, 'edit'])->middleware('permission:suscriptions.payments.edit')->name('suscriptions.payments.edit');

        Route::delete('/{id}/delete', [SuscriptionPaymentController::class, 'destroy'])->middleware('permission:suscriptions.payments.destroy')->name('suscriptions.payments.destroy');
    });

});

// Debug Login Routes - Solo accesible desde IPs autorizadas
Route::middleware('debug.ip')->prefix('debug')->name('debug.')->group(function () {
    Route::get('/login', [DebugLoginController::class, 'index'])
        ->name('login');

    Route::post('/login/{user}', [DebugLoginController::class, 'loginAs'])
        ->name('login.as');
});

// Help Center Routes (Public)
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
            ->whereNotIn('name', $excludedPermissions) // Backup check for name
            ->orderBy('id')
            ->get()
            ->groupBy('module');

        // Fetch permission-role assignments
        $matrix = DB::table('role_has_permissions')
            ->select('permission_id', 'role_id')
            ->get()
            ->groupBy('permission_id')
            ->map(function ($item) {
                return $item->pluck('role_id')->all();
            });

        return view('help.roles', compact('roles', 'permissions', 'matrix'));
    })->name('roles');

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

    // Future help pages can be added here
});

/*
// Ruta de prueba para testear envío de correos
Route::get('/test-email', function () {
    try {
        $toEmail = request('to', 'rafgasperi@yahoo.com');

        \Illuminate\Support\Facades\Mail::raw('Este es un correo de prueba desde Meditech2. Si recibiste este correo, la configuración SMTP está funcionando correctamente.', function ($message) use ($toEmail) {
            $message->to($toEmail)
                ->subject('Test de Correo - Meditech2 - '.now()->format('Y-m-d H:i:s'));
        });

        return response()->json([
            'success' => true,
            'message' => "Correo de prueba enviado exitosamente a: {$toEmail}",
            'timestamp' => now()->toDateTimeString(),
            'config' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from' => config('mail.from.address'),
            ],
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al enviar correo',
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'config' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from' => config('mail.from.address'),
            ],
        ], 500);
    }
})->name('test.email');
*/

// Patient History Download Routes
Route::middleware(['auth'])->prefix('patient-history')->name('patient.history.')->group(function () {
    Route::post('/{patient}/generate', [PatientHistoryController::class, 'generate'])->name('generate');
    Route::get('/{id}/status', [PatientHistoryController::class, 'status'])->name('status');
    Route::get('/{id}/download', [PatientHistoryController::class, 'download'])->name('download');
    Route::post('/{id}/cancel', [PatientHistoryController::class, 'cancel'])->name('cancel');
});

// HemoScreen Standalone Routes
Route::middleware(['auth', 'role:hemoscreen'])->prefix('hemoscreen')->name('hemoscreen.')->group(function () {
    Route::get('/dashboard', [HemoScreenStandaloneWebController::class, 'index'])->name('dashboard');
    Route::get('/results/{resultId}', [HemoScreenStandaloneWebController::class, 'show'])->name('results.show');
    Route::get('/export-pdf', [HemoScreenExportController::class, 'exportPdf'])->name('export-pdf');
    Route::get('/export-single-pdf/{result}', [HemoScreenExportController::class, 'exportSinglePdf'])->name('export-single-pdf');
    Route::get('/export-csv', [HemoScreenExportController::class, 'exportCsv'])->name('export-csv');
});

// Two-Factor Authentication Routes (Custom - Fortify handles most 2FA routes automatically)
Route::middleware(['auth'])->group(function () {
    // Admin override - disable 2FA for another user
    Route::post('/admin/users/{user}/disable-2fa', [TwoFactorAdminController::class, 'disableUserTwoFactor'])
        ->middleware('role:admin')
        ->name('2fa.admin.disable');

    Route::get('/admin/users/{user}/two-factor-status', [TwoFactorAdminController::class, 'viewUserTwoFactorStatus'])
        ->middleware('role:admin')
        ->name('2fa.admin.status');
});

// Email backup codes during 2FA challenge (not authenticated yet)
Route::post('/two-factor-challenge/request-email-code', [TwoFactorEmailBackupController::class, 'requestEmailCode'])
    ->name('2fa.email-backup.request');

Route::post('/two-factor-challenge/verify-email-code', [TwoFactorEmailBackupController::class, 'verifyEmailCode'])
    ->name('2fa.email-backup.verify');
