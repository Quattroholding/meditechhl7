<?php

/*
|--------------------------------------------------------------------------
| Inventory Management Routes
|--------------------------------------------------------------------------
|
| Propósito: Gestión completa del sistema de inventario incluyendo catálogo
|            de items, control de stock, transacciones y reportes.
|
| Middleware Común: ['auth', 'first.login']
|
| Controladores/Componentes Principales:
| - InventoryItemList: Listado de items de inventario
| - InventoryItemForm: Crear/editar items
| - StockManagement: Gestión de stock (recibir, ajustar, transferir, baja)
| - LowStockReport: Reporte de items con stock bajo
| - TransactionHistory: Historial de transacciones
|
| Prefijos de Rutas:
| - /inventory/items: Catálogo de items
| - /inventory/stock: Gestión de stock
| - /inventory/reports: Reportes de inventario
|
*/

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'first.login'])->prefix('inventory')->name('inventory.')->group(function () {
    // Catálogo de Items
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::get('/{itemId}/edit', [InventoryController::class, 'edit'])->name('edit');
    });

    // Gestión de Stock
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [InventoryController::class, 'stockIndex'])->name('index');
    });

    // Reportes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/low-stock', [InventoryController::class, 'lowStockReport'])->name('low-stock');
        Route::get('/transactions', [InventoryController::class, 'transactionHistory'])->name('transactions');
    });
});
