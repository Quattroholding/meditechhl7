<?php

/*
|--------------------------------------------------------------------------
| Treasury Module Routes
|--------------------------------------------------------------------------
|
| Propósito: Rutas para el módulo de tesorería
| (Bancos, Cajas, Movimientos de Tesorería)
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
*/

use App\Livewire\Treasury\Bank\BankDataTable;
use App\Livewire\Treasury\CashRegister\CashRegisterDataTable;
use App\Livewire\Treasury\TreasuryMovement\TreasuryMovementDataTable;
use App\Models\Treasury\Bank;
use App\Models\Treasury\CashRegister;
use App\Models\Treasury\TreasuryMovement;
use Illuminate\Support\Facades\Route;

// ============================================================================
// BANKS (Bancos)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'check.accounting.enabled', 'permission:treasury.banks.view'])->prefix('treasury/banks')->name('treasury.banks.')->group(function () {
    Route::get('/', BankDataTable::class)->name('index');

    Route::middleware('permission:treasury.banks.create')->group(function () {
        Route::get('/create', function () {
            return view('treasury.banks.create');
        })->name('create');
    });

    Route::middleware('permission:treasury.banks.edit')->group(function () {
        Route::get('/{bank}/edit', function () {
            return view('treasury.banks.edit');
        })->name('edit');
    });

    Route::get('/{bank}', function (Bank $bank) {
        return view('treasury.banks.show', compact('bank'));
    })->name('show');

    Route::get('/{bank}/movements', function (Bank $bank) {
        return view('treasury.banks.movements', compact('bank'));
    })->name('movements');

    Route::middleware('permission:treasury.banks.delete')->delete('/{bank}', function () {
        //
    })->name('destroy');
});

// ============================================================================
// CASH REGISTERS (Cajas)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'check.accounting.enabled', 'permission:treasury.cash-registers.view'])->prefix('treasury/cash-registers')->name('treasury.cash-registers.')->group(function () {
    Route::get('/', CashRegisterDataTable::class)->name('index');

    Route::middleware('permission:treasury.cash-registers.create')->group(function () {
        Route::get('/create', function () {
            return view('treasury.cash-registers.create');
        })->name('create');
    });

    Route::middleware('permission:treasury.cash-registers.edit')->group(function () {
        Route::get('/{cashRegister}/edit', function () {
            return view('treasury.cash-registers.edit');
        })->name('edit');
    });

    Route::get('/{cashRegister}', function (CashRegister $cashRegister) {
        return view('treasury.cash-registers.show', compact('cashRegister'));
    })->name('show');

    Route::get('/{cashRegister}/movements', function (CashRegister $cashRegister) {
        return view('treasury.cash-registers.movements', compact('cashRegister'));
    })->name('movements');

    Route::middleware('permission:treasury.cash-registers.delete')->delete('/{cashRegister}', function () {
        //
    })->name('destroy');
});

// ============================================================================
// TREASURY MOVEMENTS (Movimientos de Tesorería)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'check.accounting.enabled', 'permission:treasury.movements.view'])->prefix('treasury/movements')->name('treasury.movements.')->group(function () {
    Route::get('/', TreasuryMovementDataTable::class)->name('index');

    Route::middleware('permission:treasury.movements.create')->group(function () {
        Route::get('/create', function () {
            return view('treasury.movements.create');
        })->name('create');
    });

    Route::middleware('permission:treasury.movements.edit')->group(function () {
        Route::get('/{movement}/edit', function (TreasuryMovement $movement) {
            return view('treasury.movements.edit', compact('movement'));
        })->name('edit');
    });

    Route::get('/{movement}', function (TreasuryMovement $movement) {
        return view('treasury.movements.show', compact('movement'));
    })->name('show');

    Route::middleware('permission:treasury.movements.delete')->delete('/{movement}', function () {
        //
    })->name('destroy');
});

// ============================================================================
// TREASURY REPORTS (Reportes de Tesorería)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'check.accounting.enabled', 'permission:treasury.reports.view'])->prefix('treasury/reports')->name('treasury.reports.')->group(function () {
    Route::get('/cash-flow', function () {
        return view('treasury.reports.cash-flow');
    })->name('cash-flow');
});
