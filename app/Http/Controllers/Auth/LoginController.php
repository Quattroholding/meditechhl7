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
             $route=route('admin.dashboard');
            if(auth()->getUser()->hasRole('doctor'))   $route=route('doctor.dashboard');
            if(auth()->getUser()->hasRole('paciente')) $route=route('patient.dashboard');

            auth()->getUser()->getCurrentClient();

            return redirect()->intended($route);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
