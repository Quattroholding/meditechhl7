<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CyberSourceWebhookController extends Controller
{
    /**
     * Handle CyberSource webhook notifications
     */
    public function handle(Request $request)
    {
        // Log incoming webhook for debugging
        \Log::info('CyberSource Webhook Received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        // TODO: Implement webhook verification and processing

        return response()->json(['status' => 'received'], 200);
    }
}
