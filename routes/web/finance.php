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

use App\Livewire\Accounting\JournalEntryDataTable;
use App\Livewire\Finance\AccountsReceivable\DataTable as ReceivablesDataTable;
use App\Models\Accounting\JournalEntry;
use App\Models\Finance\AccountsReceivable;
use App\Models\Finance\CostCenter;
use App\Models\Finance\Supplier;
use App\Models\Finance\SupplierInvoice;
use App\Models\Treasury\Bank;
use App\Models\Treasury\CashRegister;
use App\Models\Treasury\PaymentMethod;
use App\Models\Treasury\TreasuryMovement;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// ============================================================================
// DOCUMENT SERVING (Servir documentos de facturas)
// ============================================================================

Route::middleware(['auth', 'verified'])->get('/documents/invoices/{path}', function ($path) {
    $filePath = 'documents/'.$path;
    $disk = Storage::disk('private');

    if (! $disk->exists($filePath)) {
        abort(404, 'Archivo no encontrado');
    }

    return response()->file($disk->path($filePath), [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="'.basename($filePath).'"',
    ]);
})->where('path', '.+')->name('invoice.document');

// ============================================================================
// COST CENTERS (Centros de Costo)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'permission:cost-centers.view'])->prefix('finance/cost-centers')->name('finance.cost-centers.')->group(function () {

    Route::middleware('permission:cost-centers.view')->group(function () {
        Route::get('/', function () {
            return view('finance.cost-centers.index');
        })->name('index');
    });

    Route::middleware('permission:cost-centers.create')->group(function () {
        Route::get('/create', function () {
            return view('finance.cost-centers.create');
        })->name('create');
    });

    Route::middleware('permission:cost-centers.edit')->group(function () {
        Route::get('/{costCenter}/edit', function (CostCenter $costCenter) {
            return view('finance.cost-centers.edit', ['costCenter' => $costCenter]);
        })->name('edit');
    });

    Route::middleware('permission:cost-centers.delete')->delete('/{costCenter}', function () {
        //
    })->name('destroy');
});

// ============================================================================
// ACCOUNTING (Contabilidad General)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'permission:accounting.view'])->prefix('accounting')->name('accounting.')->group(function () {

    // Plan de Cuentas
    Route::get('/accounts', function () {
        return view('accounting.accounts.index');
    })->name('accounts');

    Route::middleware('permission:accounting.accounts.manage')->group(function () {
        Route::delete('/accounts/{account}', function () {
            //
        })->name('accounts.destroy');
    });

    // Períodos Contables
    Route::middleware('permission:accounting.periods.manage')->prefix('periods')->name('periods.')->group(function () {
        Route::get('/', function () {
            return view('accounting.periods.index');
        })->name('index');
    });

    // Asientos Contables
    Route::middleware('permission:accounting.entries.view')->group(function () {
        // Route::get('/journal-entries', JournalEntryDataTable::class)->name('journal-entries');

        Route::get('/journal-entries', function () {
            return view('accounting.journal-entries.index');
        })->name('journal-entries');

        Route::middleware('permission:accounting.entries.create')->group(function () {
            Route::get('/journal-entries/create', function () {
                return view('accounting.journal-entries.create');
            })->name('journal-entries.create');

            Route::get('/journal-entries/{journalEntry}/edit', function (JournalEntry $journalEntry) {
                return view('accounting.journal-entries.edit', ['journalEntry' => $journalEntry]);
            })->name('journal-entries.edit');
        });

        Route::get('/journal-entries/{journalEntry}', function (JournalEntry $journalEntry) {
            return view('accounting.journal-entries.show', compact('journalEntry'));
        })->name('journal-entries.show');

        Route::delete('/journal-entries/{journalEntry}', function () {
            //
        })->name('journal-entries.destroy');
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
    // Route::get('/', ReceivablesDataTable::class)->name('index');

    Route::middleware('permission:receivables.view')->group(function () {
        Route::get('/', function () {
            return view('finance.receivables.index');
        })->name('index');
    });

    Route::get('/credit-invoices', function () {
        return view('finance.receivables.credit-invoices');
    })->name('credit-invoices');

    Route::get('/aging-report', function () {
        return view('finance.receivables.aging-report');
    })->name('aging-report');

    Route::middleware('permission:receivables.manage')->group(function () {
        Route::get('/{receivable}', function (AccountsReceivable $receivable) {
            return view('finance.receivables.show', compact('receivable'));
        })->name('show');
    });
});

// ============================================================================
// ACCOUNTS PAYABLE (Cuentas por Pagar)
// ============================================================================

Route::middleware(['auth', 'verified', 'first.login', 'permission:payables.view'])->prefix('finance/payables')->name('finance.payables.')->group(function () {

    // Proveedores
    Route::middleware('permission:payables.suppliers.manage')->prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', function () {
            return view('finance.payables.suppliers.index');
        })->name('index');

        Route::get('/create', function () {
            return view('finance.payables.suppliers.create');
        })->name('create');

        Route::get('/{supplier}/edit', function (Supplier $supplier) {
            return view('finance.payables.suppliers.edit', ['supplier' => $supplier]);
        })->name('edit');

        Route::get('/{supplier}', function (Supplier $supplier) {
            return view('finance.payables.suppliers.show', compact('supplier'));
        })->name('show');

        Route::delete('/{supplier}', function () {
            //
        })->name('destroy');
    });

    // Facturas de Proveedor
    Route::middleware('permission:payables.invoices.create')->prefix('invoices')->name('invoices.')->group(function () {

        Route::get('/', function () {
            return view('finance.payables.invoices.index');
        })->name('index');

        Route::get('/create', function () {
            return view('finance.payables.invoices.create');
        })->name('create');

        Route::get('/{invoice}/edit', function (SupplierInvoice $invoice) {
            return view('finance.payables.invoices.edit', ['invoice' => $invoice]);
        })->name('edit');

        Route::get('/{invoice}', function (SupplierInvoice $invoice) {
            return view('finance.payables.invoices.show', compact('invoice'));
        })->name('show');

        Route::delete('/{invoice}', function () {
            //
        })->name('destroy');
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

        Route::get('/{bank}/edit', function (Bank $bank) {
            return view('treasury.banks.edit', ['bank' => $bank]);
        })->name('edit');

        Route::get('/{bank}', function (Bank $bank) {
            return view('treasury.banks.show', compact('bank'));
        })->name('show');

        Route::get('/{bank}/movements', function (Bank $bank) {
            return view('treasury.banks.movements', compact('bank'));
        })->name('movements');

        Route::delete('/{bank}', function () {
            //
        })->name('destroy');
    });

    // Cajas
    Route::middleware('permission:treasury.cash-registers.manage')->prefix('cash-registers')->name('cash-registers.')->group(function () {
        Route::get('/', function () {
            return view('treasury.cash-registers.index');
        })->name('index');

        Route::get('/create', function () {
            return view('treasury.cash-registers.create');
        })->name('create');

        Route::get('/{cashRegister}/edit', function (CashRegister $cashRegister) {
            return view('treasury.cash-registers.edit', ['cashRegister' => $cashRegister]);
        })->name('edit');

        Route::get('/{cashRegister}', function (CashRegister $cashRegister) {
            return view('treasury.cash-registers.show', compact('cashRegister'));
        })->name('show');

        Route::get('/{cashRegister}/movements', function (CashRegister $cashRegister) {
            return view('treasury.cash-registers.movements', compact('cashRegister'));
        })->name('movements');

        Route::delete('/{cashRegister}', function () {
            //
        })->name('destroy');
    });

    // Métodos de Pago
    Route::middleware('permission:treasury.payment-methods.manage')->prefix('payment-methods')->name('payment-methods.')->group(function () {
        Route::get('/', function () {
            return view('treasury.payment-methods.index');
        })->name('index');

        Route::get('/create', function () {
            return view('treasury.payment-methods.create');
        })->name('create');

        Route::get('/{paymentMethod}/edit', function (PaymentMethod $paymentMethod) {
            return view('treasury.payment-methods.edit', ['paymentMethod' => $paymentMethod]);
        })->name('edit');

        Route::delete('/{paymentMethod}', function () {
            //
        })->name('destroy');
    });

    // Movimientos de Tesorería
    Route::middleware('permission:treasury.movements.create')->prefix('movements')->name('movements.')->group(function () {
        Route::get('/', function () {
            return view('treasury.movements.index');
        })->name('index');

        Route::get('/create', function () {
            return view('treasury.movements.create');
        })->name('create');

        Route::post('/', function () {
            // Handled by Livewire component
        })->name('store');

        Route::get('/{movement}/edit', function (TreasuryMovement $movement) {
            return view('treasury.movements.create', compact('movement'));
        })->name('edit');

        Route::get('/{movement}', function (TreasuryMovement $movement) {
            return view('treasury.movements.show', compact('movement'));
        })->name('show');

        Route::delete('/{movement}', function () {
            //
        })->name('destroy');
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
