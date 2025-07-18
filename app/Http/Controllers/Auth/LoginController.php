<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = auth()->user();
            $user->getCurrentClient();

            // Check if doctor needs to complete first login
            if ($user->hasRole('doctor') && is_null($user->first_login_at)) {
                return redirect()->route('first-login.show');
            }

            $route = route('admin.dashboard');
            if($user->hasRole('doctor'))   $route = route('doctor.dashboard');
            if($user->hasRole('paciente')) $route = route('patient.dashboard');
            if($user->hasRole('asistente')) $route = route('assistence.dashboard');

            return redirect()->intended($route."?show_salute=true");
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
