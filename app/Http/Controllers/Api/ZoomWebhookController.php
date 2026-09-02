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
     * Zoom requires challenge-response validation for URL verification
     */
    public function handle(Request $request)
    {
        try {
            $body = $request->getContent();
            $event = json_decode($body, true);

            // Step 1: Handle Zoom's endpoint URL validation
            // When Zoom validates your webhook URL, it sends endpoint.url_validation
            // This request does NOT include a signature, so handle it first
            if (($event['event'] ?? null) === 'endpoint.url_validation') {
                $plainToken = $event['payload']['plainToken'] ?? null;
                if ($plainToken) {
                    Log::info('Zoom webhook URL validation received and validated successfully', [
                        'plainToken' => substr($plainToken, 0, 10).'...',
                    ]);

                    return response()->json(['plainToken' => $plainToken]);
                }
            }

            // Step 2: Validate webhook signature for normal events
            // Real events (meeting.started, etc.) include a signature header
            $signature = $request->header('x-zm-signature');

            if (! $this->zoomService->validateWebhook($body, $signature)) {
                Log::warning('Invalid Zoom webhook signature', [
                    'event' => $event['event'] ?? 'unknown',
                ]);

                return response()->json(['status' => 'invalid_signature'], 403);
            }

            // Step 3: Handle the event
            $this->zoomService->handleWebhookEvent($event);

            Log::info('Zoom webhook event handled successfully', [
                'event' => $event['event'] ?? 'unknown',
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
