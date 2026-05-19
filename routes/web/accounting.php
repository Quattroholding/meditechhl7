<?php

/*
|--------------------------------------------------------------------------
| Accounting Routes
|--------------------------------------------------------------------------
|
| Propósito: Rutas para gestión financiera, incluyendo facturas, pagos,
|           cotizaciones y tipos de servicio
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Controladores Principales:
| - InvoiceController
| - PaymentController
| - QuotationController
|
| Prefijos de Rutas: /accounts, /quotations, /service-types
|
*/

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// INVOICES & PAYMENTS
// ============================================================================

Route::group(['prefix' => 'accounts', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    // ============================================================================
    // INVOICES
    // ============================================================================

    Route::group(['prefix' => 'invoices', 'middleware' => ['auth', 'verified', 'first.login', 'permission:invoices.view']], function () {

        Route::get('/', [InvoiceController::class, 'index'])->name('invoice.index');

        Route::get('/{id}', [InvoiceController::class, 'show'])->name('invoice.show');

        Route::get('/{invoice_id}/download', [InvoiceController::class, 'download'])->name('invoice.download');

    });

    // ============================================================================
    // PAYMENTS
    // ============================================================================

    Route::group(['prefix' => 'payments', 'middleware' => ['auth', 'verified', 'first.login', 'permission:payments.view']], function () {

        Route::get('/', [PaymentController::class, 'index'])->name('payment.index');

        Route::get('/{id}', [PaymentController::class, 'show'])->name('payment.show');

        Route::get('/{invoice_id}/download', [PaymentController::class, 'download'])->name('payment.download');

    });

});

// ============================================================================
// QUOTATIONS
// ============================================================================

Route::middleware(['auth', 'first.login'])->prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [QuotationController::class, 'index'])
        ->middleware('permission:quotations.view')
        ->name('index');

    Route::get('/{id}/pdf', [QuotationController::class, 'streamPdf'])
        ->middleware('permission:quotations.pdf')
        ->name('pdf.stream');

    Route::get('/{id}/download', [QuotationController::class, 'downloadPdf'])
        ->middleware('permission:quotations.pdf')
        ->name('pdf.download');

    Route::post('/{id}/status', [QuotationController::class, 'changeStatus'])
        ->middleware('permission:quotations.edit')
        ->name('status');
});

// ============================================================================
// SERVICE TYPES
// ============================================================================

Route::middleware(['auth', 'first.login', 'permission:service_types.view'])->prefix('service-types')->name('service-types.')->group(function () {
    Route::get('/', [QuotationController::class, 'serviceTypes'])->name('index');
});
