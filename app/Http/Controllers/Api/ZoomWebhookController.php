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
     */
    public function handle(Request $request)
    {
        try {
            // Zoom sends the raw body and signature in headers
            $signature = $request->header('x-zm-signature');
            $body = $request->getContent();

            // Validate webhook signature
            if (! $this->zoomService->validateWebhook($body, $signature)) {
                Log::warning('Invalid Zoom webhook signature');

                return response()->json(['status' => 'invalid_signature'], 403);
            }

            // Parse the event
            $event = json_decode($body, true);

            // Handle the event
            $this->zoomService->handleWebhookEvent($event);

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
