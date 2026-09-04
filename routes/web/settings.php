<?php

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
|
| Propósito: Configuración del sistema (plantillas, accesos rápidos, horarios, temas, firma/sello)
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Controladores Principales:
| - SettingController
|
| Prefijos de Rutas: /settings
|
*/

use App\Http\Controllers\Admin\DropboxOAuthController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ZoomOAuthController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// CONFIGURACIÓN DEL SISTEMA
// ============================================================================

Route::group(['prefix' => 'settings', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/create_consultation_template', [SettingController::class, 'consultationTemplate'])
        ->middleware('permission:settings.create_consultation_template')
        ->name('setting.create_template');

    Route::get('/create_rapid_access', [SettingController::class, 'rapidAccess'])
        ->middleware('permission:settings.create_rapid_access')
        ->name('setting.create_rapid_access');

    Route::get('/create_cpt_user', [SettingController::class, 'cptUser'])
        ->middleware('permission:settings.create_user_procedures')
        ->name('setting.create_cpt_user');

    Route::get('/create_working_hour_user', [SettingController::class, 'workingHourUser'])
        ->middleware('permission:settings.create_working_hour_user')
        ->name('setting.create_working_hour_user');

    Route::get('/create_user_procedures', [SettingController::class, 'createUserProcedure'])
        ->middleware('permission:settings.create_user_procedures')
        ->name('setting.create_user_procedures');

    Route::get('/{practitioner_id}/signature_and_seal', [SettingController::class, 'uploadSignatureSeal'])
        ->middleware('permission:settings.signature_and_seal')
        ->name('setting.signature_and_seal');

    Route::get('/theme/{client_id}', [SettingController::class, 'themeManager'])
        ->name('setting.theme_manager');

    Route::get('/invoice-template', [SettingController::class, 'invoiceTemplate'])
        ->middleware('permission:settings.invoice_template')
        ->name('setting.invoice_template');

    Route::get('/invoice-template/preview/{template}', [SettingController::class, 'invoiceTemplatePreview'])
        ->middleware('permission:settings.invoice_template')
        ->name('setting.invoice_template.preview');

    Route::get('/medical-leave-template', [SettingController::class, 'medicalLeaveTemplate'])
        ->middleware('can.manage.subscription')
        ->name('setting.medical_leave_template');

    Route::get('/medical-leave-template/preview/{template}', [SettingController::class, 'medicalLeaveTemplatePreview'])
        ->name('setting.medical_leave_template.preview');

    Route::get('/prescription-template', [SettingController::class, 'prescriptionTemplate'])
        ->middleware('can.manage.subscription')
        ->name('setting.prescription_template');

    Route::get('/prescription-template/preview/{template}', [SettingController::class, 'prescriptionTemplatePreview'])
        ->name('setting.prescription_template.preview');

    Route::get('/waitlist-settings', [SettingController::class, 'waitlistSettings'])
        ->name('setting.waitlist_settings');

    Route::get('/external-storage', function () {
        return view('settings.external-storage');
    })
        ->middleware('can.manage.subscription')
        ->name('setting.external_storage');

    // Dropbox OAuth Routes
    Route::get('/dropbox/redirect', [DropboxOAuthController::class, 'redirect'])
        ->middleware('can.manage.subscription')
        ->name('admin.dropbox.redirect');

    Route::get('/dropbox/callback', [DropboxOAuthController::class, 'callback'])
        ->name('admin.dropbox.callback');

    Route::get('/dropbox/disconnect', [DropboxOAuthController::class, 'disconnect'])
        ->middleware('can.manage.subscription')
        ->name('admin.dropbox.disconnect');

    // Zoom OAuth Routes
    Route::get('/zoom', function () {
        return view('settings.zoom-settings');
    })->name('practitioner.settings.zoom');

    Route::get('/zoom/authorize', [ZoomOAuthController::class, 'authorize'])
        ->name('zoom.authorize');

    Route::get('/zoom/callback', [ZoomOAuthController::class, 'callback'])
        ->name('zoom.callback');

    Route::post('/zoom/disconnect', [ZoomOAuthController::class, 'disconnect'])
        ->name('zoom.disconnect');

});
