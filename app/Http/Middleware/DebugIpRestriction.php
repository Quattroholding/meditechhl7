<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugIpRestriction
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('debug.enabled')) {
            abort(404);
        }

        $trustedIps = array_merge(
            config('debug.trusted_ips', []),
            config('debug.additional_ips', [])
        );

        $clientIp = $request->ip();

        if (! in_array($clientIp, $trustedIps)) {
            abort(403, 'Access denied. Your IP is not authorized for debug login.');
        }

        return $next($request);
    }
}
