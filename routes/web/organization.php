<?php

/*
|--------------------------------------------------------------------------
| Organization Management Routes
|--------------------------------------------------------------------------
|
| Propósito: Rutas para gestión de clientes, sucursales, consultorios,
|           paquetes y códigos de referido
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Controladores Principales:
| - ClientController
| - BranchController
| - RoomController
| - PackageController
| - ReferralCodeController
|
| Prefijos de Rutas: /clients, /packages, /referral-code
|
*/

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ReferralCodeController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// REFERRAL CODE (Protegido)
// ============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/referral-code/{client}/pdf', [ReferralCodeController::class, 'downloadPdf'])
        ->name('referral.code.pdf');
});

// ============================================================================
// CLIENT MANAGEMENT
// ============================================================================

Route::group(['prefix' => 'clients', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [ClientController::class, 'index'])
        ->middleware('permission:clients.view')
        ->name('client.index');

    Route::get('/referral_code', [ClientController::class, 'getReferralCode'])
        ->middleware('can.manage.subscription')
        ->name('client.referral_code');

    Route::get('/create', [ClientController::class, 'create'])
        ->middleware('permission:clients.create')
        ->name('client.create');

    Route::post('/store', [ClientController::class, 'store'])
        ->middleware('permission:clients.create')
        ->name('client.store');

    Route::get('/{id}/edit', [ClientController::class, 'edit'])
        ->middleware('permission:clients.edit')
        ->name('client.edit');

    Route::post('/{id}/update', [ClientController::class, 'update'])
        ->middleware('permission:clients.edit')
        ->name('client.update');

    Route::delete('/{id}/delete', [ClientController::class, 'destroy'])
        ->middleware('permission:clients.delete')
        ->name('client.destroy');

    // ============================================================================
    // BRANCH MANAGEMENT (Nested under clients)
    // ============================================================================

    Route::group(['prefix' => 'branch', 'middleware' => ['auth', 'verified', 'first.login']], function () {

        Route::get('/', [BranchController::class, 'index'])->name('client.branch.index');

        Route::get('/create', [BranchController::class, 'create'])->name('client.branch.create');

        Route::post('/store', [BranchController::class, 'store'])->name('client.branch.store');

        Route::get('/{id}/edit', [BranchController::class, 'edit'])->name('client.branch.edit');

        Route::post('/{id}/update', [BranchController::class, 'update'])->name('client.branch.update');

        Route::delete('/{id}/delete', [BranchController::class, 'destroy'])->name('client.branch.destroy');
    });

    // ============================================================================
    // CONSULTING ROOM MANAGEMENT (Nested under clients)
    // ============================================================================

    Route::group(['prefix' => 'consulting_rooms', 'middleware' => ['auth', 'verified', 'first.login']], function () {

        Route::get('/', [RoomController::class, 'index'])
            ->middleware('permission:rooms.view')
            ->name('client.room.index');

        Route::get('/create', [RoomController::class, 'create'])
            ->middleware('permission:rooms.create')
            ->name('client.room.create');

        Route::post('/store', [RoomController::class, 'store'])
            ->middleware('permission:rooms.create')
            ->name('client.room.store');

        Route::get('/{id}/edit', [RoomController::class, 'edit'])
            ->middleware('permission:rooms.edit')
            ->name('client.room.edit');

        Route::post('/{id}/update', [RoomController::class, 'update'])
            ->middleware('permission:rooms.edit')
            ->name('client.room.update');

        Route::delete('/{id}/delete', [RoomController::class, 'destroy'])
            ->middleware('permission:rooms.delete')
            ->name('client.room.destroy');
    });

});

// ============================================================================
// PACKAGE MANAGEMENT
// ============================================================================

Route::group(['prefix' => 'packages', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [PackageController::class, 'index'])
        ->middleware('permission:manage-packages')
        ->name('package.index');

    Route::get('/create', [PackageController::class, 'create'])
        ->middleware('permission:manage-packages')
        ->name('package.create');

    Route::post('/store', [PackageController::class, 'store'])
        ->middleware('permission:manage-packages')
        ->name('package.store');

    Route::get('/{id}/edit', [PackageController::class, 'edit'])
        ->middleware('permission:manage-packages')
        ->name('package.edit');

    Route::post('/{id}/update', [PackageController::class, 'update'])
        ->middleware('permission:manage-packages')
        ->name('package.update');

    Route::delete('/{id}/delete', [PackageController::class, 'destroy'])
        ->middleware('permission:manage-packages')
        ->name('package.destroy');

});
