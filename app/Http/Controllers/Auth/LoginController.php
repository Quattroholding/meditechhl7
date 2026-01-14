<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Validar Turnstile solo en producción (excepto cuando viene de force_login)
        if (config('app.env') === 'production' && ! $request->has('force_login')) {
            $turnstileResponse = $request->input('cf-turnstile-response');

            if (! $turnstileResponse || ! $this->validateTurnstile($turnstileResponse)) {
                return back()->withErrors([
                    'cf-turnstile-response' => 'Por favor, completa la verificación de seguridad.',
                ])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Verificar si el usuario tiene sesiones activas en otros dispositivos
            $activeSessions = $this->getActiveSessions($user->id);

            // Si hay sesiones activas y no se ha confirmado cerrarlas
            if ($activeSessions->isNotEmpty() && ! $request->has('force_login')) {
                // Guardar datos antes de cerrar sesión
                $pendingData = [
                    'pending_login_user_id' => $user->id,
                    'pending_login_email' => $credentials['email'],
                    'pending_login_password' => encrypt($credentials['password']),
                    'active_sessions_data' => $activeSessions->toArray(),
                    'sessions_count' => $activeSessions->count(),
                ];

                // Cerrar la sesión actual temporal
                Auth::logout();

                // Regenerar sesión (esto crea una nueva sesión limpia)
                $request->session()->invalidate();
                $request->session()->regenerate();

                // AHORA sí guardar los datos en la nueva sesión
                foreach ($pendingData as $key => $value) {
                    session()->put($key, $value);
                }

                // Guardar la sesión explícitamente para asegurar que persiste
                session()->save();

                // Redirigir a página de confirmación
                return redirect()->route('login.concurrent-session');
            }

            // Si se confirmó force_login, cerrar otras sesiones
            if ($request->has('force_login')) {
                $this->terminateOtherSessions($user->id);
                // Limpiar datos de sesión pendiente
                session()->forget(['pending_login_user_id', 'pending_login_email', 'pending_login_password', 'active_sessions_data', 'sessions_count']);
            }
            $request->session()->regenerate();

            $user = auth()->user();
            $user->getCurrentClient();

            // Check if doctor needs to complete first login
            if (is_null($user->first_login_at)) {
                return redirect()->route('first-login.show');
            }

            $route = route('admin.dashboard');
            if ($user->hasRole('doctor')) {
                $route = route('doctor.dashboard');
            }
            if ($user->hasRole('paciente')) {
                $route = route('patient.dashboard');
            }
            if ($user->hasRole('recepcionista')) {
                $route = route('assistence.dashboard');
            }
            if ($user->hasRole('admin client')) {
                $route = route('client.dashboard');
            }

            if ($user->hasRole('contabilidad')) {
                $route = route('accounting.dashboard');
            }

            if ($user->hasRole('validador')) {
                $route =  route('user.pending-validations');
            }

            return redirect()->intended($route.'?show_salute=true');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Validate Turnstile token with Cloudflare API
     */
    private function validateTurnstile(string $token): bool
    {
        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $token,
        ]);

        if ($response->successful()) {
            $result = $response->json();

            return $result['success'] ?? false;
        }

        return false;
    }

    /**
     * Get active sessions for a user
     */
    private function getActiveSessions(int $userId)
    {
        $sessionLifetime = config('session.lifetime', 120);

        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>', now()->subMinutes($sessionLifetime)->timestamp)
            ->get();
    }

    /**
     * Terminate all other sessions for a user
     */
    private function terminateOtherSessions(int $userId): void
    {
        $currentSessionId = session()->getId();

        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }

    /**
     * Show concurrent session warning page
     */
    public function showConcurrentSession(Request $request)
    {
        \Log::info('showConcurrentSession called', [
            'session_id' => session()->getId(),
            'has_pending_user_id' => session()->has('pending_login_user_id'),
            'has_pending_email' => session()->has('pending_login_email'),
            'has_pending_password' => session()->has('pending_login_password'),
            'sessions_count' => session('sessions_count'),
            'all_session_keys' => array_keys(session()->all()),
        ]);

        if (! session()->has('pending_login_user_id')) {
            \Log::warning('No pending_login_user_id in session, redirecting to login');

            return redirect()->route('login')
                ->with('error', 'La sesión expiró. Por favor, inicia sesión nuevamente.');
        }

        $sessionsCount = session('sessions_count', 1);

        return view('auth.concurrent-session', compact('sessionsCount'));
    }

    /**
     * Cancel the login attempt and return to login page
     */
    public function cancelLogin(Request $request)
    {
        session()->forget(['pending_login_user_id', 'pending_login_email', 'pending_login_password', 'active_sessions_data', 'sessions_count']);

        return redirect()->route('login')
            ->with('info', 'Inicio de sesión cancelado. Por favor, cierre su sesión anterior primero.');
    }
}
