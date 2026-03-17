<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FirstLoginController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        // Redirect if user has already completed first login
        if (! is_null($user->first_login_at)) {
            return redirect()->route('dashboard');
        }

        return view('auth.first-login');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $route = route('profile.edit', $user->id);
        // Validate if user hasn't completed first login
        if ($user->hasRole('hemoscreen')) {
            $route = route('hemoscreen.dashboard');
        }else if ($user->practitioner) {
            $route = route('practitioner.profile', $user->practitioner->id);
        }else if($user->patient){
            $route = route('patient.profile', $user->patient->id);
        }
        if (! is_null($user->first_login_at)) {
            return redirect($route);
        }


        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es requerida',
            'password.required' => 'La nueva contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'La confirmación de contraseña no coincide',
        ]);

        // Verify current password
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta']);
        }

        // Update password and mark first login as completed
        $user->update([
            'password' => Hash::make($request->password),
            'first_login_at' => now(),
        ]);

        return redirect($route)->with('success', 'Contraseña actualizada exitosamente. Bienvenido al sistema.');
    }
}
