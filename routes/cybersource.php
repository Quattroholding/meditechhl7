<?php

use App\Http\Controllers\TestCyberSourceController;
use App\Http\Controllers\Webhooks\CyberSourceWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CyberSource Payment Routes
|--------------------------------------------------------------------------
|
| Rutas relacionadas con la integración de CyberSource para pagos
| con tarjeta de crédito, tokenización y webhooks.
|
| NOTA: La tokenización de tarjetas durante el registro ahora se maneja
| directamente en el formulario de registro (/register/client) usando
| CyberSource Microform. No se requieren rutas adicionales de pago.
|
*/

// ============================================================================
// CYBERSOURCE WEBHOOKS
// ============================================================================

Route::post('/cybersource', [CyberSourceWebhookController::class, 'handle'])
    ->name('webhooks.cybersource');

Route::get(
    '/test/cybersource/tms-customers',
    [TestCyberSourceController::class, 'customers']
);

// ============================================================================
// FUTURE ROUTES (Commented for reference)
// ============================================================================

/*
// Payment Method Management (Protected routes)
Route::middleware(['auth', 'verified'])->prefix('payment-methods')->name('payment.methods.')->group(function () {
    Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
    Route::post('/', [PaymentMethodController::class, 'store'])->name('store');
    Route::delete('/{paymentToken}', [PaymentMethodController::class, 'destroy'])->name('destroy');
    Route::post('/{paymentToken}/default', [PaymentMethodController::class, 'setDefault'])->name('default');
});

// Manual Retry Payment (Protected routes)
Route::middleware(['auth', 'verified'])->prefix('invoices')->name('invoices.')->group(function () {
    Route::post('/{invoice}/retry-payment', [InvoiceController::class, 'retryPayment'])->name('retry');
});
*/
