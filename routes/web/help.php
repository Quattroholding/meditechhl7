<?php

/*
|--------------------------------------------------------------------------
| Help Center Routes (Public)
|--------------------------------------------------------------------------
|
| Propósito: Centro de ayuda público con documentación y tutoriales del sistema
|
| Middleware: Ninguno (rutas públicas)
|
| Prefijos de Rutas: /help
|
*/

use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

// ============================================================================
// HELP CENTER - RUTAS PÚBLICAS
// ============================================================================

Route::prefix('help')->name('help.')->group(function () {

    Route::get('/', function () {
        return view('help.index');
    })->name('index');

    Route::get('/registration', function () {
        return view('help.registration');
    })->name('registration');

    Route::get('/branches', function () {
        return view('help.branches');
    })->name('branches');

    Route::get('/consulting-rooms', function () {
        return view('help.consulting-rooms');
    })->name('consulting-rooms');

    Route::get('/patients', function () {
        return view('help.patients');
    })->name('patients');

    Route::get('/medical-history', function () {
        return view('help.medical-history');
    })->name('medical-history');

    Route::get('/appointments', function () {
        return view('help.appointments');
    })->name('appointments');

    Route::get('/settings', function () {
        return view('help.settings');
    })->name('settings');

    Route::get('/external-storage', function () {
        return view('help.external-storage');
    })->name('external-storage');

    Route::get('/consultation', function () {
        return view('help.consultation');
    })->name('consultation');

    Route::get('/billing', function () {
        return view('help.billing');
    })->name('billing');

    Route::get('/payments', function () {
        return view('help.payments');
    })->name('payments');

    Route::get('/subscriptions', function () {
        return view('help.subscriptions');
    })->name('subscriptions');

    Route::get('/doctor-dashboard', function () {
        return view('help.doctor-dashboard');
    })->name('doctor-dashboard');

    Route::get('/profile', function () {
        return view('help.profile');
    })->name('profile');

    Route::get('/support', function () {
        return view('help.support');
    })->name('support');

    Route::get('/service-requests', function () {
        return view('help.service-requests');
    })->name('service-requests');

    Route::get('/users', function () {
        return view('help.users');
    })->name('users');

    Route::get('/packages', function () {
        return view('help.packages');
    })->name('packages');

    Route::get('/quotations', function () {
        return view('help.quotations');
    })->name('quotations');

    Route::get('/medicines', function () {
        return view('help.medicines');
    })->name('medicines');

    Route::get('/medical-directory', function () {
        return view('help.medical-directory');
    })->name('medical-directory');

    Route::get('/doctors', function () {
        return view('help.doctors');
    })->name('doctors');

    Route::get('/2fa', function () {
        return view('help.2fa');
    })->name('2fa');

    Route::get('/inventory', function () {
        return view('help.inventory');
    })->name('inventory');

    Route::get('/roles', function () {
        $roles = Rol::whereIn('id', [5, 2, 6, 3, 4])
            ->get()
            ->sortBy(function ($role) {
                return array_search($role->id, [5, 2, 6, 3, 4]);
            });

        $excludedModules = ['roles', 'clientes', 'encuestas', 'reportes', 'aseguradoras', 'paquetes', 'hemoscreen', 'cotizaciones', 'tickets', 'dashboards'];
        $excludedPermissions = [
            'Validar usuarios registrados desde la aplicación móvil',
            'Eliminar registros de pacientes',
            'Eliminar registros de consultas',
            'Editar información de medicamentos',
            'Eliminar medicamentos del catálogo',
            'Seleccionar plantilla de recetas medicas',
            'Acceso al dashboard de contabilidad',
            'Editar factura de suscripcion',
            'Editar pago de suscripcion',
            'Cancelar pago de suscripcion',
            'Verificar pago de suscripcion',
            'Ver lista de suscripciones',
            'Comentar cambiar estauts',
            'Asignar ticket',
        ];

        $permissions = Permission::whereNotIn('module', $excludedModules)
            ->whereNotIn('description', $excludedPermissions)
            ->whereNotIn('name', $excludedPermissions)
            ->orderBy('id')
            ->get()
            ->groupBy('module');

        $matrix = DB::table('role_has_permissions')
            ->select('permission_id', 'role_id')
            ->get()
            ->groupBy('permission_id')
            ->map(function ($item) {
                return $item->pluck('role_id')->all();
            });

        return view('help.roles', compact('roles', 'permissions', 'matrix'));
    })->name('roles');

});
