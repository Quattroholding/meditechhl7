<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugLoginController extends Controller
{
    /**
     * Mostrar interfaz de selección de usuario
     */
    public function index(Request $request)
    {
        // Obtener filtros
        $role = $request->get('role');
        $search = $request->get('search');

        // Query base
        $query = User::query()->where('active', 1)->with('roles');

        // Filtrar por rol si se especifica
        if ($role) {
            $query->role($role);
        }

        // Buscar por nombre o email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
            });
        }

        // Obtener usuarios paginados
        $users = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        // Obtener roles disponibles
        $roles = \Spatie\Permission\Models\Role::all();

        return view('debug.login', compact('users', 'roles', 'role', 'search'));
    }

    /**
     * Hacer login con usuario seleccionado
     */
    public function loginAs(Request $request, User $user)
    {
        if (! $user->active) {
            return back()->with('error', 'Este usuario está inactivo.');
        }

        // Hacer login sin validar contraseña
        Auth::login($user);

        // Regenerar sesión por seguridad
        $request->session()->regenerate();

        // Determinar ruta de redirección según rol
        $route = route('admin.dashboard');

        if ($user->hasRole('doctor')) {
            $route = route('doctor.dashboard');
        } elseif ($user->hasRole('paciente')) {
            $route = route('patient.dashboard');
        } elseif ($user->hasRole('recepcionista')) {
            $route = route('assistence.dashboard');
        } elseif ($user->hasRole('contabilidad')) {
            $route = route('accounting.dashboard');
        } elseif ($user->hasRole('admin client')) {
            $route = route('client.dashboard');
        }

        return redirect($route.'?show_salute=true')
            ->with('success', "Login exitoso como {$user->full_name} ({$user->email})");
    }
}
