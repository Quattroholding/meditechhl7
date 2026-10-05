<?php

/*
|--------------------------------------------------------------------------
| Finance Module Routes
|--------------------------------------------------------------------------
|
| Propósito: Rutas para el módulo financiero del cliente
| (Contabilidad, Cuentas por Cobrar, Cuentas por Pagar, Tesorería)
|
| Middleware Común: ['auth', 'verified', 'first.login']
|
| Prefijos de Rutas: /finance, /accounting, /treasury
|
*/

use App\Livewire\Accounting\AccountDataTable;
use App\Livewire\Accounting\JournalEntryDataTable;
use App\Livewire\Finance\AccountsPayable\InvoiceDataTable;
use App\Livewire\Finance\AccountsReceivable\DataTable as ReceivablesDataTable;
use App\Livewire\Finance\CostCenter\DataTable as CostCenterDataTable;
use App\Livewire\Finance\Supplier\DataTable as SupplierDataTable;
use Illuminate\Support\Facades\Route;

// ============================================================================
// COST CENTERS (Centros de Costo)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'permission:cost-centers.view'])->prefix('finance/cost-centers')->name('finance.cost-centers.')->group(function () {
    Route::get('/', CostCenterDataTable::class)->name('index');

    Route::middleware('permission:cost-centers.create')->group(function () {
        Route::get('/create', function () {
            return view('finance.cost-centers.create');
        })->name('create');
    });

    Route::middleware('permission:cost-centers.edit')->group(function () {
        Route::get('/{costCenter}/edit', function () {
            return view('finance.cost-centers.edit');
        })->name('edit');
    });
});

// ============================================================================
// ACCOUNTING (Contabilidad General)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'permission:accounting.view'])->prefix('accounting')->name('accounting.')->group(function () {

    // Plan de Cuentas
    Route::get('/accounts', AccountDataTable::class)->name('accounts');

    // Asientos Contables
    Route::middleware('permission:accounting.entries.view')->group(function () {
        Route::get('/journal-entries', JournalEntryDataTable::class)->name('journal-entries');

        Route::middleware('permission:accounting.entries.create')->group(function () {
            Route::get('/journal-entries/create', function () {
                return view('accounting.journal-entries.create');
            })->name('journal-entries.create');

            Route::get('/journal-entries/{journalEntry}/edit', function () {
                return view('accounting.journal-entries.edit');
            })->name('journal-entries.edit');
        });

        Route::get('/journal-entries/{journalEntry}', function () {
            return view('accounting.journal-entries.show');
        })->name('journal-entries.show');
    });

    // Reportes Contables
    Route::middleware('permission:accounting.reports.view')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/trial-balance', function () {
            return view('accounting.reports.trial-balance');
        })->name('trial-balance');

        Route::get('/balance-sheet', function () {
            return view('accounting.reports.balance-sheet');
        })->name('balance-sheet');

        Route::get('/income-statement', function () {
            return view('accounting.reports.income-statement');
        })->name('income-statement');

        Route::get('/cost-centers', function () {
            return view('accounting.reports.cost-centers');
        })->name('cost-centers');
    });
});

// ============================================================================
// ACCOUNTS RECEIVABLE (Cuentas por Cobrar)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'permission:receivables.view'])->prefix('finance/receivables')->name('finance.receivables.')->group(function () {
    Route::get('/', ReceivablesDataTable::class)->name('index');

    Route::get('/credit-invoices', function () {
        return view('finance.receivables.credit-invoices');
    })->name('credit-invoices');

    Route::get('/aging-report', function () {
        return view('finance.receivables.aging-report');
    })->name('aging-report');

    Route::middleware('permission:receivables.manage')->group(function () {
        Route::get('/{receivable}', function () {
            return view('finance.receivables.show');
        })->name('show');
    });
});

// ============================================================================
// ACCOUNTS PAYABLE (Cuentas por Pagar)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'permission:payables.view'])->prefix('finance/payables')->name('finance.payables.')->group(function () {

    // Proveedores
    Route::middleware('permission:payables.suppliers.manage')->prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', SupplierDataTable::class)->name('index');

        Route::get('/create', function () {
            return view('finance.payables.suppliers.create');
        })->name('create');

        Route::get('/{supplier}/edit', function () {
            return view('finance.payables.suppliers.edit');
        })->name('edit');

        Route::get('/{supplier}', function () {
            return view('finance.payables.suppliers.show');
        })->name('show');
    });

    // Facturas de Proveedor
    Route::middleware('permission:payables.invoices.create')->prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', InvoiceDataTable::class)->name('index');

        Route::get('/create', function () {
            return view('finance.payables.invoices.create');
        })->name('create');

        Route::get('/{invoice}/edit', function () {
            return view('finance.payables.invoices.edit');
        })->name('edit');

        Route::get('/{invoice}', function () {
            return view('finance.payables.invoices.show');
        })->name('show');
    });

    // Reporte de Antigüedad
    Route::get('/aging-report', function () {
        return view('finance.payables.aging-report');
    })->name('aging-report');
});

// ============================================================================
// TREASURY (Tesorería)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'permission:treasury.view'])->prefix('treasury')->name('treasury.')->group(function () {

    // Bancos
    Route::middleware('permission:treasury.banks.manage')->prefix('banks')->name('banks.')->group(function () {
        Route::get('/', function () {
            return view('treasury.banks.index');
        })->name('index');

        Route::get('/create', function () {
            return view('treasury.banks.create');
        })->name('create');

        Route::get('/{bank}/edit', function () {
            return view('treasury.banks.edit');
        })->name('edit');

        Route::get('/{bank}', function () {
            return view('treasury.banks.show');
        })->name('show');

        Route::get('/{bank}/movements', function () {
            return view('treasury.banks.movements');
        })->name('movements');
    });

    // Cajas
    Route::middleware('permission:treasury.cash-registers.manage')->prefix('cash-registers')->name('cash-registers.')->group(function () {
        Route::get('/', function () {
            return view('treasury.cash-registers.index');
        })->name('index');

        Route::get('/create', function () {
            return view('treasury.cash-registers.create');
        })->name('create');

        Route::get('/{cashRegister}/edit', function () {
            return view('treasury.cash-registers.edit');
        })->name('edit');

        Route::get('/{cashRegister}', function () {
            return view('treasury.cash-registers.show');
        })->name('show');

        Route::get('/{cashRegister}/movements', function () {
            return view('treasury.cash-registers.movements');
        })->name('movements');
    });

    // Movimientos de Tesorería
    Route::middleware('permission:treasury.movements.create')->prefix('movements')->name('movements.')->group(function () {
        Route::get('/', function () {
            return view('treasury.movements.index');
        })->name('index');

        Route::get('/create', function () {
            return view('treasury.movements.create');
        })->name('create');

        Route::get('/{movement}/edit', function () {
            return view('treasury.movements.edit');
        })->name('edit');

        Route::get('/{movement}', function () {
            return view('treasury.movements.show');
        })->name('show');
    });

    // Reportes de Tesorería
    Route::middleware('permission:treasury.reports.view')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/cash-flow', function () {
            return view('treasury.reports.cash-flow');
        })->name('cash-flow');

        Route::get('/bank-reconciliation', function () {
            return view('treasury.reports.bank-reconciliation');
        })->name('bank-reconciliation');

        Route::get('/movement-summary', function () {
            return view('treasury.reports.movement-summary');
        })->name('movement-summary');
    });
});
