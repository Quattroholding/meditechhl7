<?php

/*
|--------------------------------------------------------------------------
| User Management Routes
|--------------------------------------------------------------------------
|
| Propósito: Rutas para gestión de usuarios, roles, permisos, perfil,
|           autenticación de dos factores, notificaciones y tickets
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Controladores Principales:
| - UserController
| - RoleController
| - PermissionController
| - ProfileController
| - NotificationController
| - TwoFactorAdminController
| - TwoFactorEmailBackupController
|
| Prefijos de Rutas: /users, /roles, /permissions, /profile, /notifications, /tickets
|
*/

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TwoFactorAdminController;
use App\Http\Controllers\TwoFactorEmailBackupController;
use App\Http\Controllers\UserController;
use App\Livewire\User\Profile;
use Illuminate\Support\Facades\Route;

// ============================================================================
// PROFILE & USER SETTINGS
// ============================================================================

Route::middleware(['auth', 'first.login'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Profile Route (Livewire)
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

// ============================================================================
// USER MANAGEMENT
// ============================================================================

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('user.index');

    Route::get('/create', [UserController::class, 'create'])
        ->middleware(['permission:users.create', 'can.manage.subscription'])
        ->name('user.create');

    Route::get('/change_client/{client_id}', [UserController::class, 'changeClient'])
        ->middleware('permission:users.change_client')
        ->name('user.change_client');

    Route::post('/store', [UserController::class, 'store'])
        ->middleware(['permission:users.create', 'can.manage.subscription'])
        ->name('user.store');

    Route::get('/{id}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.edit')
        ->name('user.edit');

    Route::post('/{id}/update', [UserController::class, 'update'])
        ->middleware('permission:users.edit')
        ->name('user.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('user.destroy');

    Route::post('/{id}/activate', [UserController::class, 'activate'])
        ->middleware('permission:users.activate')
        ->name('user.activate');

    Route::get('/pending-validations', function () {
        return view('users.pending-validations');
    })->middleware('permission:users.validate')
        ->name('user.pending-validations');

});

// ============================================================================
// ROLES & PERMISSIONS
// ============================================================================

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

// ============================================================================
// TWO-FACTOR AUTHENTICATION MANAGEMENT
// ============================================================================

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
