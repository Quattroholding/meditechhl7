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
            ? array_map('trim', explode(',', $ips))
            : config('app.allowed_ips', ['127.0.0.1', '::1']);

        // Get client IP (handles proxies like Cloudflare)
        // Cloudflare sends the real IP in CF-Connecting-IP header
        $clientIp = $request->header('CF-Connecting-IP')
            ?? $request->header('X-Forwarded-For')
            ?? $request->ip();

        // Debug: uncomment to see what's happening
        // \Log::info('IP Restriction Debug', ['clientIp' => $clientIp, 'allowedIps' => $allowedIps]);

        // Check if IP is allowed (case-insensitive, trim whitespace)
        if (! in_array(trim($clientIp), $allowedIps, true)) {
            abort(403, sprintf(
                'Acceso no autorizado desde esta IP: %s. IPs permitidas: %s',
                $clientIp,
                implode(', ', $allowedIps)
            ));
        }

        return $next($request);
    }
}
