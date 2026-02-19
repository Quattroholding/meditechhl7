<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verify webhook (Meta requires this for initial setup)
     * GET request from Meta to verify the webhook URL
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub.mode');
        $token = $request->query('hub.verify_token');
        $challenge = $request->query('hub.challenge');

        // Verify token matches what we set in Meta dashboard
        $verifyToken = config('services.meta.webhook_verify_token', 'meditech_whatsapp_webhook_2026');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('WhatsApp webhook verified successfully', [
                'mode' => $mode,
                'token' => $token,
            ]);

            // Meta expects the challenge back as plain text
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
            'expected_token' => $verifyToken,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming webhooks from Meta
     * POST request with webhook data
     */
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('WhatsApp webhook received', [
                'payload' => $payload,
                'headers' => $request->headers->all(),
            ]);

            // Save raw webhook
            $this->storeWebhook($payload);

            // Process webhook data
            $this->processWebhook($payload);

            // Meta expects a 200 OK response
            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            Log::error('Error processing WhatsApp webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            // Still return 200 to prevent Meta from retrying
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Store webhook in database for debugging
     */
    protected function storeWebhook(array $payload): void
    {
        try {
            // Extract relevant data from Meta webhook structure
            $entry = $payload['entry'][0] ?? null;
            $changes = $entry['changes'][0] ?? null;
            $value = $changes['value'] ?? null;

            // Determine event type
            $eventType = $this->determineEventType($value);

            // Extract message/status data
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
                'payload' => $payload,
            ]);
        }
    }

    /**
     * Process webhook and take actions
     */
    protected function processWebhook(array $payload): void
    {
        $entry = $payload['entry'][0] ?? null;
        $changes = $entry['changes'][0] ?? null;
        $value = $changes['value'] ?? null;

        if (! $value) {
            return;
        }

        // Handle different webhook types
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

    /**
     * Handle message status updates (sent, delivered, read, failed)
     */
    protected function handleStatusUpdate(array $statuses): void
    {
        foreach ($statuses as $status) {
            Log::info('WhatsApp message status update', [
                'message_id' => $status['id'] ?? null,
                'status' => $status['status'] ?? null,
                'recipient' => $status['recipient_id'] ?? null,
                'timestamp' => $status['timestamp'] ?? null,
                'errors' => $status['errors'] ?? null,
            ]);

            // If status is failed, log the error
            if (($status['status'] ?? null) === 'failed') {
                $errors = $status['errors'] ?? [];
                foreach ($errors as $error) {
                    Log::error('WhatsApp message failed', [
                        'message_id' => $status['id'] ?? null,
                        'error_code' => $error['code'] ?? null,
                        'error_title' => $error['title'] ?? null,
                        'error_message' => $error['message'] ?? null,
                        'error_details' => $error['error_data']['details'] ?? null,
                    ]);
                }
            }
        }
    }

    /**
     * Handle incoming messages (if needed in future)
     */
    protected function handleIncomingMessage(array $messages): void
    {
        foreach ($messages as $message) {
            Log::info('WhatsApp incoming message', [
                'message_id' => $message['id'] ?? null,
                'from' => $message['from'] ?? null,
                'type' => $message['type'] ?? null,
                'text' => $message['text']['body'] ?? null,
            ]);

            // Here you could implement auto-responses or ticket creation
        }
    }

    /**
     * Handle errors
     */
    protected function handleError(array $errors): void
    {
        foreach ($errors as $error) {
            Log::error('WhatsApp webhook error', [
                'error_code' => $error['code'] ?? null,
                'error_title' => $error['title'] ?? null,
                'error_message' => $error['message'] ?? null,
                'error_details' => $error,
            ]);
        }
    }

    /**
     * Determine event type from webhook value
     */
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

    /**
     * Extract message data from webhook value
     */
    protected function extractMessageData(?array $value): array
    {
        if (! $value) {
            return [];
        }

        $data = [];

        // Extract from status updates
        if (isset($value['statuses'][0])) {
            $status = $value['statuses'][0];
            $data['message_id'] = $status['id'] ?? null;
            $data['status'] = $status['status'] ?? null;
            $data['to'] = $status['recipient_id'] ?? null;

            // Extract error if failed
            if (($status['status'] ?? null) === 'failed' && isset($status['errors'][0])) {
                $error = $status['errors'][0];
                $data['error_message'] = ($error['title'] ?? '').' - '.($error['message'] ?? '');
            }

            $data['metadata'] = [
                'timestamp' => $status['timestamp'] ?? null,
                'conversation' => $status['conversation'] ?? null,
                'pricing' => $status['pricing'] ?? null,
            ];
        }

        // Extract from incoming messages
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
