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

            // Step 1: Handle Zoom's URL validation challenge
            // When Zoom validates your webhook URL, it sends a challenge and expects it back
            if ($event['event'] === 'app_deauthorization') {
                $challenge = $event['payload']['challenge'] ?? null;
                if ($challenge) {
                    Log::info('Zoom webhook URL validation challenge received and responded');

                    return response()->json(['plainToken' => $challenge]);
                }
            }

            // Step 2: Validate webhook signature for normal events
            $signature = $request->header('x-zm-signature');

            if (! $this->zoomService->validateWebhook($body, $signature)) {
                Log::warning('Invalid Zoom webhook signature', [
                    'event' => $event['event'] ?? 'unknown',
                ]);

                return response()->json(['status' => 'invalid_signature'], 403);
            }

            // Step 3: Handle the event
            $this->zoomService->handleWebhookEvent($event);

            Log::info('Zoom webhook handled successfully', [
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
