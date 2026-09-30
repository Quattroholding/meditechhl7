<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index')->middleware('can:documents.view');
        Route::get('create', [DocumentController::class, 'create'])->name('create')->middleware('can:documents.create');
        Route::post('/', [DocumentController::class, 'store'])->name('store')->middleware('can:documents.upload');
        Route::get('{document}', [DocumentController::class, 'show'])->name('show');
        Route::get('{document}/detail', [DocumentController::class, 'detail'])->name('detail');
        Route::get('{document}/view', [DocumentController::class, 'view'])->name('view')->middleware('can:documents.download');
        Route::get('{document}/download', [DocumentController::class, 'download'])->name('download')->middleware('can:documents.download');
        Route::delete('{document}', [DocumentController::class, 'destroy'])->name('destroy');
    });
});
