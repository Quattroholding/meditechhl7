<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiDocsIpRestriction
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('api-docs.enabled')) {
            abort(404);
        }

        $trustedIps = array_merge(
            config('api-docs.trusted_ips', []),
            config('api-docs.additional_ips', [])
        );

        $clientIp = $request->ip();

        if (! in_array($clientIp, $trustedIps)) {
            abort(403, 'Access denied. Your IP is not authorized to access API documentation.');
        }

        return $next($request);
    }
}
