<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictByIp
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string|null  $ips  Comma-separated list of allowed IPs (optional)
     */
    public function handle(Request $request, Closure $next, ?string $ips = null): Response
    {
        // Get allowed IPs from parameter or config
        $allowedIps = $ips
            ? explode(',', $ips)
            : config('app.allowed_ips', ['127.0.0.1', '::1']);

        // Get client IP (handles proxies like Cloudflare)
        // Cloudflare sends the real IP in CF-Connecting-IP header
        $clientIp = $request->header('CF-Connecting-IP')
            ?? $request->header('X-Forwarded-For')
            ?? $request->ip();

        // Check if IP is allowed
        if (! in_array($clientIp, $allowedIps)) {
            abort(403, sprintf(
                'Acceso no autorizado desde esta IP: %s. IPs permitidas: %s',
                $clientIp,
                implode(', ', $allowedIps)
            ));
        }

        return $next($request);
    }
}
