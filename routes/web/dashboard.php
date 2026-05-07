<?php

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Propósito: Rutas de dashboards por rol (admin, doctor, paciente, cliente, recepción, contabilidad)
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Controladores Principales:
| - DashboardController
|
| Prefijos de Rutas: /dashboard
|
*/

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// DASHBOARD DISPATCHER - Redirige a dashboard según rol del usuario
// ============================================================================

Route::get('/dash', function () {
    // Default fallback route
    $route = route('login');

    if (auth()->user()->hasRole('admin')) {
        $route = route('admin.dashboard');
    }
    if (auth()->user()->hasRole('admin client')) {
        $route = route('client.dashboard');
    }
    if (auth()->user()->hasRole('doctor')) {
        $route = route('doctor.dashboard');
    }
    if (auth()->user()->hasRole('paciente')) {
        $route = route('patient.dashboard');
    }
    if (auth()->user()->hasRole('recepcionista') or auth()->user()->hasRole('asistente medico')) {
        $route = route('assistence.dashboard');
    }
    if (auth()->user()->hasRole('contabilidad')) {
        $route = route('accounting.dashboard');
    }
    if (auth()->user()->hasRole('hemoscreen')) {
        $route = route('hemoscreen.dashboard');
    }

    dd(auth()->user()->hasRole('ventas'));

    if (auth()->user()->hasRole('ventas')) {
        $route = route('quotations.index');
    }

    return redirect($route);
})->name('dash')->middleware(['auth', 'verified', 'first.login']);

// ============================================================================
// DASHBOARDS POR ROL
// ============================================================================

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/admin', [DashboardController::class, 'admin'])
        ->middleware('permission:dashboard.admin')
        ->name('admin.dashboard');

    Route::get('/admin-kpis', function () {
        return view('Dashboard.admin-kpis');
    })->name('admin.dashboard.kpis')
        ->middleware('role:admin');

    Route::get('/doctor', [DashboardController::class, 'doctor'])
        ->middleware('permission:dashboard.doctor')
        ->name('doctor.dashboard');

    Route::get('/patient', [DashboardController::class, 'patient'])
        ->middleware('permission:dashboard.patient')
        ->name('patient.dashboard');

    Route::get('/client', [DashboardController::class, 'admin_client'])
        ->middleware('permission:dashboard.client')
        ->name('client.dashboard');

    Route::get('/recepcionist', [DashboardController::class, 'assistence'])
        ->middleware('permission:dashboard.assistence')
        ->name('assistence.dashboard');

    Route::get('/accounting', [DashboardController::class, 'accounting'])
        ->middleware('permission:dashboard.accounting')
        ->name('accounting.dashboard');

});
