<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZoomWebhookController extends Controller
{
    public function __construct(protected ZoomService $zoomService) {}

    /**
     * Handle incoming Zoom webhooks
     * Uses custom header authentication instead of signature verification
     */
    public function handle(Request $request)
    {
        try {
            $body = $request->getContent();

            // Validate JSON
            if (empty($body)) {
                Log::warning('Zoom webhook received with empty body');

                return response()->json(['status' => 'bad_request'], 400);
            }

            $event = json_decode($body, true);

            // Check if JSON is valid
            if (! is_array($event)) {
                Log::warning('Zoom webhook received with invalid JSON', [
                    'body_length' => strlen($body),
                    'json_error' => json_last_error_msg(),
                ]);

                return response()->json(['status' => 'invalid_json'], 400);
            }

            // Step 1: Handle Zoom's endpoint URL validation
            // When Zoom validates your webhook URL, it sends endpoint.url_validation
            if (($event['event'] ?? null) === 'endpoint.url_validation') {
                $plainToken = $event['payload']['plainToken'] ?? null;
                if ($plainToken) {
                    Log::info('Zoom webhook URL validation received', [
                        'plainToken' => substr($plainToken, 0, 10).'...',
                    ]);

                    return response()->json(['plainToken' => $plainToken]);
                }
            }

            // Step 2: Validate custom header authentication
            // Check that the x-zoom-token header matches our configured webhook token
            $headerToken = $request->header('x-zoom-token');
            $expectedToken = config('services.zoom.webhook_secret');

            if (! $headerToken || ! $expectedToken || $headerToken !== $expectedToken) {
                Log::warning('Invalid Zoom webhook authentication', [
                    'event' => $event['event'] ?? 'unknown',
                    'has_header' => (bool) $headerToken,
                    'has_configured_token' => (bool) $expectedToken,
                ]);

                return response()->json(['status' => 'unauthorized'], 401);
            }

            // Step 3: Validate that event array has required structure
            if (! isset($event['event'])) {
                Log::warning('Zoom webhook missing event field', [
                    'keys' => array_keys($event),
                ]);

                return response()->json(['status' => 'invalid_event'], 400);
            }

            // Step 4: Handle the event
            $this->zoomService->handleWebhookEvent($event);

            Log::info('Zoom webhook event processed successfully', [
                'event' => $event['event'],
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Error handling Zoom webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
