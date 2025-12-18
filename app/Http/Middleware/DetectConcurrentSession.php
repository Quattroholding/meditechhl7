<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DetectConcurrentSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar a usuarios autenticados
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = session()->getId();

            // Verificar si hay otras sesiones activas para este usuario
            $activeSessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId)
                ->where('last_activity', '>', now()->subMinutes(config('session.lifetime', 120))->timestamp)
                ->get();

            // Si hay otras sesiones activas, guardar información en sesión
            if ($activeSessions->isNotEmpty()) {
                session(['has_concurrent_session' => true]);
                session(['other_sessions_count' => $activeSessions->count()]);
            }
        }

        return $next($request);
    }
}
