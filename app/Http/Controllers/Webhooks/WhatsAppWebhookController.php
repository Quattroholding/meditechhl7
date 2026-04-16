<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\WhatsappAgent;
use App\Models\WhatsAppWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verify webhook (Meta requires this for initial setup)
     */
    public function verify(Request $request)
    {

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.meta.webhook_verify_token', 'meditech_whatsapp_webhook_2026');

        Log::info('WhatsApp webhook verification attempt', [
            'mode' => $mode,
            'token_received' => $token,
            'token_expected' => $verifyToken,
            'challenge' => $challenge,
            'all_query_params' => $request->query(),
        ]);

        if ($mode === 'subscribe' && $token === $verifyToken) {

            Log::info('WhatsApp webhook verified successfully');

            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming webhook POST from Meta
     */
    public function handle(Request $request)
    {

        try {

            $payload = $request->all();

            Log::info('WhatsApp webhook received', [
                'payload' => $payload,
            ]);

            // Guardar webhook para debugging
            $this->storeWebhook($payload);

            // Obtener phone_number_id
            $phoneNumberId = data_get(
                $payload,
                'entry.0.changes.0.value.metadata.phone_number_id'
            );

            if (! $phoneNumberId) {

                Log::warning('phone_number_id not found in webhook');

                return response()->json(['status' => 'ok']);
            }

            Log::info('Webhook phone_number_id detected', [
                'phone_number_id' => $phoneNumberId,
            ]);

            Log::info('Webhook value', [
                'value' => data_get($payload, 'entry.0.changes.0.value'),
            ]);

            /**
             * ROUTING CONFIG
             *
             * Aquí decides qué números van a n8n
             * y cuáles procesa Laravel
             */
            $agent = WhatsappAgent::where('phone_number_id', $phoneNumberId)
                ->where('active', true)
                ->first();

            if (! $agent) {

                Log::warning('No agent configured for phone_number_id', [
                    'phone_number_id' => $phoneNumberId,
                ]);

                return response()->json(['status' => 'ok']);
            }

            if ($agent->type === 'n8n') {

                /*Http::timeout(10)->post(
                    $agent->webhook_url,
                    $payload
                );*/

                // Obtener la URL base del API según el entorno
                $apiBaseUrl = config('app.url').'/api/v1/';

                Http::post($agent->webhook_url, [
                    'phone_number_id' => $phoneNumberId,
                    'payload' => $payload,   // opcional: todo el payload original
                    'api_base_url' => $agent->api_base_url, // URL base del API para que n8n la use
                ]);
            }

            if ($agent->type === 'laravel') {

                $this->processWebhook($payload);
            }

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {

            Log::error('Error processing WhatsApp webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error'], 200);
        }
    }

    /**
     * Store webhook in database for debugging
     */
    protected function storeWebhook(array $payload): void
    {

        try {

            $entry = $payload['entry'][0] ?? null;
            $changes = $entry['changes'][0] ?? null;
            $value = $changes['value'] ?? null;

            $eventType = $this->determineEventType($value);

            $messageData = $this->extractMessageData($value);

            WhatsAppWebhook::create([
                'event_type' => $eventType,
                'message_id' => $messageData['message_id'] ?? null,
                'phone_number_id' => $value['metadata']['phone_number_id'] ?? null,
                'from' => $messageData['from'] ?? null,
                'to' => $messageData['to'] ?? null,
                'status' => $messageData['status'] ?? null,
                'error_message' => $messageData['error_message'] ?? null,
                'raw_payload' => $payload,
                'metadata' => $messageData['metadata'] ?? null,
                'webhook_received_at' => now(),
            ]);

        } catch (\Exception $e) {

            Log::error('Error storing webhook', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process webhook for Laravel logic
     */
    protected function processWebhook(array $payload): void
    {

        $entry = $payload['entry'][0] ?? null;
        $changes = $entry['changes'][0] ?? null;
        $value = $changes['value'] ?? null;

        if (! $value) {
            return;
        }

        if (isset($value['statuses'])) {
            $this->handleStatusUpdate($value['statuses']);
        }

        if (isset($value['messages'])) {
            $this->handleIncomingMessage($value['messages']);
        }

        if (isset($value['errors'])) {
            $this->handleError($value['errors']);
        }
    }

    protected function handleStatusUpdate(array $statuses): void
    {

        foreach ($statuses as $status) {

            Log::info('WhatsApp message status update', [
                'message_id' => $status['id'] ?? null,
                'status' => $status['status'] ?? null,
            ]);
        }
    }

    protected function handleIncomingMessage(array $messages): void
    {

        foreach ($messages as $message) {

            Log::info('Incoming WhatsApp message (Laravel)', [
                'from' => $message['from'] ?? null,
                'text' => $message['text']['body'] ?? null,
            ]);
        }
    }

    protected function handleError(array $errors): void
    {

        foreach ($errors as $error) {

            Log::error('WhatsApp webhook error', [
                'code' => $error['code'] ?? null,
                'message' => $error['message'] ?? null,
            ]);
        }
    }

    protected function determineEventType(?array $value): string
    {

        if (! $value) {
            return 'unknown';
        }

        if (isset($value['statuses'])) {
            return 'status';
        }

        if (isset($value['messages'])) {
            return 'message';
        }

        if (isset($value['errors'])) {
            return 'error';
        }

        return 'other';
    }

    protected function extractMessageData(?array $value): array
    {

        if (! $value) {
            return [];
        }

        $data = [];

        if (isset($value['messages'][0])) {

            $message = $value['messages'][0];

            $data['message_id'] = $message['id'] ?? null;
            $data['from'] = $message['from'] ?? null;
            $data['status'] = 'received';

            $data['metadata'] = [
                'type' => $message['type'] ?? null,
                'text' => $message['text']['body'] ?? null,
                'timestamp' => $message['timestamp'] ?? null,
            ];
        }

        return $data;
    }
}
