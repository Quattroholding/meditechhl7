<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Validar Turnstile solo en producción
        if (config('app.env') === 'production') {
            $turnstileResponse = $request->input('cf-turnstile-response');

            if (! $turnstileResponse || ! $this->validateTurnstile($turnstileResponse)) {
                return back()->withErrors([
                    'cf-turnstile-response' => 'Por favor, completa la verificación de seguridad.',
                ])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials)) {
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
            if ($user->hasRole('asistente')) {
                $route = route('assistence.dashboard');
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
}
