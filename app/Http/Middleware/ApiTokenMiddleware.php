<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string|null  $scope  Optional scope to check
     * @param  string|null  $requiredMode  Optional mode to check (integrated or standalone)
     */
    public function handle(Request $request, Closure $next, ?string $scope = null, ?string $requiredMode = null): Response
    {
        // Get token from header
        $token = $this->extractTokenFromRequest($request);

        if (! $token) {
            return $this->unauthorizedResponse('Token no proporcionado');
        }

        // Get client IP
        $clientIp = $this->getClientIp($request);

        // Validate token (with caching for performance)
        $apiToken = $this->validateToken($token, $clientIp, $scope, $requiredMode);

        if (! $apiToken) {
            $this->logUnauthorizedAttempt($token, $clientIp, $scope, $requiredMode);

            return $this->unauthorizedResponse('Token inválido, IP no autorizada, o modo incorrecto');
        }

        // Update last used information (async to avoid slowing down requests)
        dispatch(function () use ($apiToken, $clientIp) {
            $apiToken->updateLastUsed($clientIp);
        })->afterResponse();

        // Load practitioner relationship if exists
        $apiToken->load('practitioner');

        // Add token info to request for potential use in controllers
        $request->merge([
            'api_token' => $apiToken,
            'token_name' => $apiToken->name,
            'token_scopes' => $apiToken->scopes,
            'authenticated_practitioner' => $apiToken->practitioner,
        ]);

        return $next($request);
    }

    /**
     * Extract token from request
     */
    private function extractTokenFromRequest(Request $request): ?string
    {
        // Try Bearer token first
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Try X-API-Token header
        $apiTokenHeader = $request->header('X-API-Token');
        if ($apiTokenHeader) {
            return $apiTokenHeader;
        }

        // Try query parameter (less secure, but useful for testing)
        return $request->query('api_token');
    }

    /**
     * Get client IP address
     */
    private function getClientIp(Request $request): string
    {
        $ip = $request->ip();

        // Handle proxies and load balancers
        if ($request->hasHeader('X-Forwarded-For')) {
            $forwardedIps = explode(',', $request->header('X-Forwarded-For'));
            $ip = trim($forwardedIps[0]);
        } elseif ($request->hasHeader('X-Real-IP')) {
            $ip = $request->header('X-Real-IP');
        }

        return $ip;
    }

    /**
     * Validate token with caching
     */
    private function validateToken(string $token, string $clientIp, ?string $scope = null, ?string $requiredMode = null): ?ApiToken
    {
        // Cache key for token validation
        $cacheKey = "api_token_validation:{$token}:{$clientIp}:".($scope ?? 'no_scope').':'.($requiredMode ?? 'no_mode');

        // Try to get from cache first (5 minutes cache)
        $cachedResult = Cache::get($cacheKey);

        if ($cachedResult !== null) {
            return $cachedResult === false ? null : $cachedResult;
        }

        // Find token in database
        $apiToken = ApiToken::where('token', $token)->active()->first();

        if (! $apiToken) {
            Cache::put($cacheKey, false, 300); // Cache negative result for 5 minutes

            return null;
        }

        // Check IP restriction
        if (! $apiToken->isValidForIp($clientIp)) {
            Cache::put($cacheKey, false, 300);

            return null;
        }

        // Check scope if specified
        if ($scope && ! $apiToken->hasScope($scope)) {
            Cache::put($cacheKey, false, 300);

            return null;
        }

        // Check mode if specified
        if ($requiredMode && $apiToken->mode !== $requiredMode) {
            Cache::put($cacheKey, false, 300);

            return null;
        }

        // Cache positive result for 5 minutes
        Cache::put($cacheKey, $apiToken, 300);

        return $apiToken;
    }

    /**
     * Log unauthorized attempt
     */
    private function logUnauthorizedAttempt(string $token, string $ip, ?string $scope = null, ?string $requiredMode = null): void
    {
        Log::warning('Unauthorized API token attempt', [
            'token_prefix' => substr($token, 0, 10).'...',
            'ip' => $ip,
            'scope' => $scope,
            'required_mode' => $requiredMode,
            'user_agent' => request()->header('User-Agent'),
            'url' => request()->url(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => $message,
            'code' => 'API_TOKEN_INVALID',
        ], 401);
    }
}
