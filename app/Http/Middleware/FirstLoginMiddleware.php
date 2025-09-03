<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FirstLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Check if user has doctor role and hasn't completed first login
            if (is_null($user->first_login_at)) {
                // Skip middleware for password change routes to avoid infinite redirect
                if (! $request->routeIs('first-login.*') && ! $request->routeIs('logout')) {
                    return redirect()->route('first-login.show');
                }
            }
        }

        return $next($request);
    }
}
