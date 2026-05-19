<?php

/*
|--------------------------------------------------------------------------
| Practitioner Routes
|--------------------------------------------------------------------------
|
| Propósito: Gestión de médicos/profesionales de la salud (CRUD, perfil, directorio, firma/sello)
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Controladores Principales:
| - PractitionerController
| - FileController (para archivos privados: firma y sello)
|
| Prefijos de Rutas: /practitioners, /files/practitioner
|
*/

use App\Http\Controllers\FileController;
use App\Http\Controllers\PractitionerController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// CRUD DE PRACTITIONERS
// ============================================================================

Route::group(['prefix' => 'practitioners', 'middleware' => ['auth', 'verified', 'first.login']], function () {

    Route::get('/', [PractitionerController::class, 'index'])
        ->middleware('permission:practitioners.view')
        ->name('practitioner.index');

    Route::get('/directory', [PractitionerController::class, 'directory'])
        ->middleware('permission:practitioners.directory')
        ->name('practitioner.directory');

    Route::get('/create', [PractitionerController::class, 'create'])
        ->middleware('permission:practitioners.create')
        ->name('practitioner.create');

    Route::post('/store', [PractitionerController::class, 'store'])
        ->middleware('permission:practitioners.create')
        ->name('practitioner.store');

    Route::get('/{id}/profile', [PractitionerController::class, 'profile'])
        ->middleware('permission:practitioners.profile')
        ->name('practitioner.profile');

    Route::get('/{id}/edit', [PractitionerController::class, 'edit'])
        ->middleware('permission:practitioners.edit')
        ->name('practitioner.edit');

    Route::post('/{id}/update', [PractitionerController::class, 'update'])
        ->middleware('permission:practitioners.edit')
        ->name('practitioner.update');

    Route::delete('/{id}', [PractitionerController::class, 'destroy'])
        ->middleware('permission:practitioners.delete')
        ->name('practitioner.destroy');

});

// ============================================================================
// ARCHIVOS PRIVADOS DE PRACTITIONERS (Firma y Sello)
// ============================================================================

Route::middleware(['auth', 'first.login'])->group(function () {

    // Private File Serving Routes
    Route::get('/practitioner/{practitioner_id}/signature', [FileController::class, 'serveSignature'])
        ->name('practitioner.signature');

    Route::get('/practitioner/{practitioner_id}/seal', [FileController::class, 'serveSeal'])
        ->name('practitioner.seal');

});
