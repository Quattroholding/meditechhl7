<?php

/*
|--------------------------------------------------------------------------
| Subscription Management Routes
|--------------------------------------------------------------------------
|
| Propósito: Rutas para gestión de suscripciones, facturas de suscripción
|           y pagos de suscripción
|
| Middleware Común: ['auth', 'verified', 'first.login', 'can.manage.subscription']
|
| Controladores Principales:
| - SuscriptionController
| - SuscriptionInvoiceController
| - SuscriptionPaymentController
|
| Prefijos de Rutas: /suscriptions
|
*/

use App\Http\Controllers\SuscriptionController;
use App\Http\Controllers\SuscriptionInvoiceController;
use App\Http\Controllers\SuscriptionPaymentController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// SUBSCRIPTION MANAGEMENT
// ============================================================================

Route::group(['prefix' => 'suscriptions', 'middleware' => ['auth', 'verified', 'first.login', 'can.manage.subscription']], function () {

    // Lista de todas las suscripciones (solo administradores)
    Route::get('/list', [SuscriptionController::class, 'index'])
        ->middleware('permission:suscriptions.manage')
        ->name('suscriptions.index');

    Route::get('/', [SuscriptionController::class, 'show'])
        ->middleware('permission:suscriptions.show')
        ->name('suscriptions.show');

    Route::get('/edit/{subscription}', [SuscriptionController::class, 'edit'])
        ->middleware('permission:suscriptions.manage')
        ->name('suscriptions.edit');

    Route::get('/upgrade', [SuscriptionController::class, 'upgrade'])
        ->middleware('permission:suscriptions.show')
        ->name('suscriptions.upgrade');

    Route::post('/upgrade', [SuscriptionController::class, 'processUpgrade'])
        ->middleware('permission:suscriptions.show')
        ->name('suscriptions.upgrade.process');

    Route::post('/reactivate', [SuscriptionController::class, 'reactivate'])
        ->middleware('permission:suscriptions.show')
        ->name('suscriptions.reactivate');

    // ============================================================================
    // SUBSCRIPTION INVOICES
    // ============================================================================

    Route::group(['prefix' => 'invoices', 'middleware' => ['auth', 'verified', 'first.login']], function () {

        Route::get('/', [SuscriptionInvoiceController::class, 'index'])
            ->middleware('permission:suscriptions.invoices.index')
            ->name('suscriptions.invoices.index');

        Route::get('/pending', [SuscriptionInvoiceController::class, 'pending'])
            ->middleware('permission:suscriptions.invoices.pending')
            ->name('suscriptions.invoices.pending');

        Route::get('/{id}', [SuscriptionInvoiceController::class, 'show'])
            ->middleware('permission:suscriptions.invoices.show')
            ->name('suscriptions.invoices.show');

        Route::get('/{id}/download', [SuscriptionInvoiceController::class, 'download'])
            ->middleware('permission:suscriptions.invoices.download')
            ->name('suscriptions.invoices.download');

        Route::get('/{id}/edit', [SuscriptionInvoiceController::class, 'edit'])
            ->middleware('permission:suscriptions.invoices.edit')
            ->name('suscriptions.invoices.edit');

        Route::delete('/{id}/delete', [SuscriptionInvoiceController::class, 'destroy'])
            ->middleware('permission:suscriptions.invoices.destroy')
            ->name('suscriptions.invoices.destroy');
    });

    // ============================================================================
    // SUBSCRIPTION PAYMENTS
    // ============================================================================

    Route::group(['prefix' => 'payments', 'middleware' => ['auth', 'verified', 'first.login']], function () {

        Route::get('/', [SuscriptionPaymentController::class, 'index'])
            ->middleware('permission:suscriptions.payments.index')
            ->name('suscriptions.payments.index');

        Route::get('/settings', [SuscriptionPaymentController::class, 'settings'])
            ->middleware('permission:suscriptions.payments.settings')
            ->name('suscriptions.payments.settings');

        Route::get('/{id}/verify', [SuscriptionPaymentController::class, 'verify'])
            ->middleware('permission:suscriptions.payments.verify')
            ->name('suscriptions.payments.verify');

        Route::post('/{id}/reject', [SuscriptionPaymentController::class, 'reject'])
            ->middleware('permission:suscriptions.payments.verify')
            ->name('suscriptions.payments.reject');

        Route::get('/{id}/download-receipt', [SuscriptionPaymentController::class, 'downloadReceipt'])
            ->middleware('permission:suscriptions.payments.show')
            ->name('suscriptions.payments.download-receipt');

        Route::get('/{id}', [SuscriptionPaymentController::class, 'show'])
            ->middleware('permission:suscriptions.payments.show')
            ->name('suscriptions.payments.show');

        Route::get('/{id}/download', [SuscriptionPaymentController::class, 'download'])
            ->middleware('permission:suscriptions.payments.download')
            ->name('suscriptions.payments.download');

        Route::get('/{id}/edit', [SuscriptionPaymentController::class, 'edit'])
            ->middleware('permission:suscriptions.payments.edit')
            ->name('suscriptions.payments.edit');

        Route::delete('/{id}/delete', [SuscriptionPaymentController::class, 'destroy'])
            ->middleware('permission:suscriptions.payments.destroy')
            ->name('suscriptions.payments.destroy');
    });

});
