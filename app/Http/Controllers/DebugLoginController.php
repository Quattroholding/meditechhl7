<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class DebugLoginController extends Controller
{
    /**
     * Mostrar interfaz de selección de usuario
     */
    public function index(Request $request)
    {
        // Obtener filtros
        $role = $request->get('role');
        $package = $request->get('package');
        $search = $request->get('search');

        // Query base
        $query = User::query()->where('active', 1)->with('roles')->with('clients.package');

        // Filtrar por rol si se especifica
        if ($role) {
            $query->role($role);
        }

        if ($package) {
            $query->package($package);
        }

        // Buscar por nombre o email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('clients', function ($q2) use ($search) {
                    $q2->where('clients.name', 'like', '%'.$search.'%');
                });
                $q->orWhere('email', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
            });
        }

        // Obtener usuarios paginados
        $users = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        // Obtener roles disponibles
        $roles = Role::all();
        // Obtener paquetes disponibles
        $packages = Package::all();

        return view('debug.login', compact('users', 'roles', 'role', 'packages', 'package', 'search'));
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

        if ($user->hasRole('doctor') or $user->hasRole('asistente medico')) {
            $route = route('doctor.dashboard');
        } elseif ($user->hasRole('paciente')) {
            $route = route('patient.dashboard');
        } elseif ($user->hasRole('recepcionista')) {
            $route = route('assistence.dashboard');
        } elseif ($user->hasRole('contabilidad')) {
            $route = route('accounting.dashboard');
        } elseif ($user->hasRole('admin client')) {
            $route = route('client.dashboard');
        } elseif ($user->hasRole('validador')) {
            $route = route('user.pending-validations');
        }elseif ($user->hasRole('ventas')) {
            $route = route('quotations.index');
        }

        return redirect($route.'?show_salute=true')
            ->with('success', "Login exitoso como {$user->full_name} ({$user->email})");
    }
}
