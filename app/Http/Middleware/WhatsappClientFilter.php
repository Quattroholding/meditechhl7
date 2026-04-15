<?php

namespace App\Http\Middleware;

use App\Models\WhatsappAgent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class WhatsappClientFilter
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks for a phone_number_id header and resolves
     * the associated client_id from the whatsapp_agents table.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get phone_number_id from multiple sources (header with different formats or query param)
        // Note: Nginx blocks headers with underscores by default, so query param is most reliable
        $phoneNumberId = $request->header('phone_number_id')
            ?? $request->header('phone-number-id')
            ?? $request->header('X-Phone-Number-Id')
            ?? $request->query('phone_number_id');

        // If phone_number_id exists and is not null
        if (! empty($phoneNumberId)) {
            // Find the whatsapp agent by phone_number_id
            $whatsappAgent = WhatsappAgent::where('phone_number_id', $phoneNumberId)
                ->where('active', true)
                ->first();

            // If agent found and has a client_id, store it in request attributes and config
            if ($whatsappAgent && ! is_null($whatsappAgent->client_id)) {
                $request->attributes->set('whatsapp_client_id', $whatsappAgent->client_id);
            }
        }

        return $next($request);
    }
}
