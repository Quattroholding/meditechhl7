<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('{documentUpload}', [DocumentController::class, 'show'])->name('show');
        Route::get('{documentUpload}/view', [DocumentController::class, 'view'])->name('view');
        Route::get('{documentUpload}/download', [DocumentController::class, 'download'])->name('download');
        Route::delete('{documentUpload}', [DocumentController::class, 'destroy'])->name('destroy');
    });
});
