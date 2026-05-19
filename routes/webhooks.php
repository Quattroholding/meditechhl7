<?php

use App\Http\Controllers\Api\HemoScreenController;
use App\Http\Controllers\Api\HemoScreenStandaloneController;
use App\Http\Controllers\SuscriptionPaymentController;
use App\Http\Controllers\Webhooks\WhatsAppWebhookController;
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

// HemoScreen API (Integrated & Standalone)
// All HemoScreen endpoints are now accessed via webhooks.meditecpty.com
Route::prefix('api/v1')->middleware('api.token')->group(function () {

    // HemoScreen Integrated - Used during consultations with ServiceRequest
    // URL: webhooks.meditecpty.com/api/v1/lab/hemoscreen
    Route::post('/lab/hemoscreen', HemoScreenController::class)
        ->middleware('api.token:hemoscreen,integrated')
        ->name('webhooks.hemoscreen.integrated');

    // HemoScreen Service Request Lookup - Get ServiceRequest by hemo_identification
    // URL: webhooks.meditecpty.com/api/v1/lab/hemoscreen/service-request/{hemo_identification}
    Route::get('/lab/hemoscreen/service-request/{hemo_identification}', [HemoScreenController::class, 'getServiceRequest'])
        ->middleware('api.token:hemoscreen,integrated')
        ->name('webhooks.hemoscreen.service-request');

    // HemoScreen Standalone - For standalone users (no clinic management)
    // URL: webhooks.meditecpty.com/api/v1/lab/hemoscreen-standlone
    Route::post('/lab/hemoscreen-standlone', HemoScreenStandaloneController::class)
        ->middleware('api.token:hemoscreen-standalone,standalone')
        ->name('webhooks.hemoscreen.standalone');
});
