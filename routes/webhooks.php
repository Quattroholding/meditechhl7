<?php

use App\Http\Controllers\Webhooks\WhatsAppWebhookController;
use App\Http\Controllers\SuscriptionPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
| These routes are accessed via webhooks.meditecpty.com subdomain
| URL Format: webhooks.meditecpty.com/{provider}
|
| Examples:
| - webhooks.meditecpty.com/whatsapp (WhatsApp Meta)
| - webhooks.meditecpty.com/cybersource (CyberSource)
| - webhooks.meditecpty.com/twilio (Twilio)
|
*/

// Test route without middleware
Route::get('/test', function () {
    return response('Webhook endpoint is working!', 200);
});

// WhatsApp Meta Webhooks
// URL: webhooks.meditecpty.com/whatsapp
Route::prefix('whatsapp')->name('webhooks.whatsapp.')->group(function () {
    // GET: Webhook verification (Meta uses this to verify the endpoint)
    Route::get('/', [WhatsAppWebhookController::class, 'verify'])->name('verify');

    // POST: Webhook handler (Meta sends webhook data here)
    Route::post('/', [WhatsAppWebhookController::class, 'handle'])->name('handle');
});

Route::get('/subscriptions/payments/yappy-ipn', [SuscriptionPaymentController::class, 'yappyIPN'])->name('suscriptions.payments.yappy_ipn');
