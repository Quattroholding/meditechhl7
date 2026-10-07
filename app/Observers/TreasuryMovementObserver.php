<?php

namespace App\Observers;

use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\AccountingPeriod;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Treasury\TreasuryMovement;
use Illuminate\Support\Str;

class TreasuryMovementObserver
{
    /**
     * Handle the TreasuryMovement "created" event.
     * Generates a journal entry automatically when a treasury movement is created.
     */
    public function created(TreasuryMovement $treasuryMovement): void
    {
        try {
            // Get the open accounting period for the client
            $period = AccountingPeriod::where('client_id', $treasuryMovement->client_id)
                ->where('status', 'open')
                ->where('start_date', '<=', $treasuryMovement->movement_date)
                ->where('end_date', '>=', $treasuryMovement->movement_date)
                ->first();

            if (! $period) {
                return; // No open period found, skip journal entry creation
            }

            $description = "Movimiento de Tesorería: {$treasuryMovement->description}";

            $entry = JournalEntry::create([
                'uuid' => Str::uuid(),
                'client_id' => $treasuryMovement->client_id,
                'entry_number' => $this->generateEntryNumber($treasuryMovement->client_id),
                'entry_date' => $treasuryMovement->movement_date,
                'document_type' => 'treasury_movement',
                'document_number' => $treasuryMovement->movement_number,
                'description' => $description,
                'status' => 'draft',
                'accounting_period_id' => $period->id,
                'source_type' => TreasuryMovement::class,
                'source_id' => $treasuryMovement->id,
                'created_by' => $treasuryMovement->created_by,
            ]);

            // Create journal entry lines based on movement type
            $this->createJournalLines($entry, $treasuryMovement);

            // Update the treasury movement with the journal entry id
            $treasuryMovement->update(['journal_entry_id' => $entry->id]);

            // Auto-post the entry if balanced
            if ($this->isBalanced($entry)) {
                $entry->update(['status' => 'posted', 'posted_at' => now(), 'posted_by' => $treasuryMovement->created_by]);
                $this->updateAccountBalances($entry);
                $this->updateBankAndCashBalances($treasuryMovement);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating journal entry for treasury movement: '.$e->getMessage());
        }
    }

    /**
     * Create journal entry lines based on movement type
     */
    private function createJournalLines(JournalEntry $entry, TreasuryMovement $treasuryMovement): void
    {
        if ($treasuryMovement->isDeposit()) {
            // DEPOSIT: DEBIT Bank, CREDIT Income/Adjustment
            if ($treasuryMovement->bank && $treasuryMovement->bank->accounting_account_id) {
                JournalEntryLine::create([
                    'uuid' => Str::uuid(),
                    'journal_entry_id' => $entry->id,
                    'accounting_account_id' => $treasuryMovement->bank->accounting_account_id,
                    'debit' => $treasuryMovement->amount,
                    'credit' => 0,
                    'description' => "Depósito en {$treasuryMovement->bank->bank_name}",
                ]);
            }

            // Find a deposit income account (temporary - should be configurable)
            $depositAccount = $this->findOrCreateTemporaryAccount($treasuryMovement->client_id, 'Ingreso de Depósito');
            if ($depositAccount) {
                JournalEntryLine::create([
                    'uuid' => Str::uuid(),
                    'journal_entry_id' => $entry->id,
                    'accounting_account_id' => $depositAccount->id,
                    'debit' => 0,
                    'credit' => $treasuryMovement->amount,
                    'description' => "Origen: {$treasuryMovement->description}",
                ]);
            }
        } elseif ($treasuryMovement->isWithdrawal()) {
            // WITHDRAWAL: DEBIT Expense/Adjustment, CREDIT Bank
            if ($treasuryMovement->bank && $treasuryMovement->bank->accounting_account_id) {
                JournalEntryLine::create([
                    'uuid' => Str::uuid(),
                    'journal_entry_id' => $entry->id,
                    'accounting_account_id' => $treasuryMovement->bank->accounting_account_id,
                    'debit' => 0,
                    'credit' => $treasuryMovement->amount,
                    'description' => "Retiro de {$treasuryMovement->bank->bank_name}",
                ]);
            }

            // Find a withdrawal expense account
            $expenseAccount = $this->findOrCreateTemporaryAccount($treasuryMovement->client_id, 'Gasto de Retiro');
            if ($expenseAccount) {
                JournalEntryLine::create([
                    'uuid' => Str::uuid(),
                    'journal_entry_id' => $entry->id,
                    'accounting_account_id' => $expenseAccount->id,
                    'debit' => $treasuryMovement->amount,
                    'credit' => 0,
                    'description' => "Destino: {$treasuryMovement->description}",
                ]);
            }
        } elseif ($treasuryMovement->isTransfer()) {
            // TRANSFER: DEBIT Destination Bank, CREDIT Source Bank
            if ($treasuryMovement->bank && $treasuryMovement->bank->accounting_account_id) {
                JournalEntryLine::create([
                    'uuid' => Str::uuid(),
                    'journal_entry_id' => $entry->id,
                    'accounting_account_id' => $treasuryMovement->bank->accounting_account_id,
                    'debit' => 0,
                    'credit' => $treasuryMovement->amount,
                    'description' => "Transferencia desde {$treasuryMovement->bank->bank_name}",
                ]);
            }

            if ($treasuryMovement->cashRegister && $treasuryMovement->cashRegister->accounting_account_id) {
                JournalEntryLine::create([
                    'uuid' => Str::uuid(),
                    'journal_entry_id' => $entry->id,
                    'accounting_account_id' => $treasuryMovement->cashRegister->accounting_account_id,
                    'debit' => $treasuryMovement->amount,
                    'credit' => 0,
                    'description' => "Transferencia a {$treasuryMovement->cashRegister->name}",
                ]);
            }
        }
    }

    /**
     * Check if journal entry is balanced (total debits = total credits)
     */
    private function isBalanced(JournalEntry $entry): bool
    {
        $totalDebit = $entry->journalEntryLines()->sum('debit');
        $totalCredit = $entry->journalEntryLines()->sum('credit');

        return abs($totalDebit - $totalCredit) < 0.01; // Allow for rounding errors
    }

    /**
     * Update account balances after posting
     */
    private function updateAccountBalances(JournalEntry $entry): void
    {
        foreach ($entry->journalEntryLines as $line) {
            if ($line->accountingAccount) {
                $debit = (float) $line->debit;
                $credit = (float) $line->credit;

                // For assets and expenses, debit increases balance; credit decreases
                if (in_array($line->accountingAccount->account_type, ['asset', 'expense', 'cost'])) {
                    $line->accountingAccount->increment('balance', $debit - $credit);
                } else {
                    // For liabilities, equity, and income, credit increases balance
                    $line->accountingAccount->increment('balance', $credit - $debit);
                }
            }
        }
    }

    /**
     * Update Bank and CashRegister balances based on movement type
     */
    private function updateBankAndCashBalances(TreasuryMovement $treasuryMovement): void
    {
        if ($treasuryMovement->isDeposit()) {
            if ($treasuryMovement->bank) {
                $treasuryMovement->bank->increment('balance', $treasuryMovement->amount);
            }
            if ($treasuryMovement->cashRegister) {
                $treasuryMovement->cashRegister->increment('balance', $treasuryMovement->amount);
            }
        } elseif ($treasuryMovement->isWithdrawal()) {
            if ($treasuryMovement->bank) {
                $treasuryMovement->bank->decrement('balance', $treasuryMovement->amount);
            }
            if ($treasuryMovement->cashRegister) {
                $treasuryMovement->cashRegister->decrement('balance', $treasuryMovement->amount);
            }
        } elseif ($treasuryMovement->isTransfer()) {
            // Transfer: decrease from source, increase to destination
            if ($treasuryMovement->bank) {
                $treasuryMovement->bank->decrement('balance', $treasuryMovement->amount);
            }
            if ($treasuryMovement->cashRegister) {
                $treasuryMovement->cashRegister->increment('balance', $treasuryMovement->amount);
            }
        }
    }

    /**
     * Generate unique entry number
     */
    private function generateEntryNumber(int $clientId): string
    {
        $year = now()->year;
        $lastEntry = JournalEntry::where('client_id', $clientId)
            ->whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $sequence = $lastEntry ? (int) substr($lastEntry->entry_number, -6) + 1 : 1;

        return sprintf('JE-%d-%06d', $year, $sequence);
    }

    /**
     * Find or create a temporary account for deposits/withdrawals
     * This is a temporary solution until the user configures accounting event configs
     */
    private function findOrCreateTemporaryAccount(int $clientId, string $accountName): ?AccountingAccount
    {
        $account = AccountingAccount::where('client_id', $clientId)
            ->where('name', $accountName)
            ->first();

        if ($account) {
            return $account;
        }

        // Try to find a general income or expense account to use
        if (str_contains($accountName, 'Ingreso')) {
            return AccountingAccount::where('client_id', $clientId)
                ->where('account_type', 'income')
                ->where('status', 'active')
                ->where('allows_transaction', true)
                ->first();
        } else {
            return AccountingAccount::where('client_id', $clientId)
                ->where('account_type', 'expense')
                ->where('status', 'active')
                ->where('allows_transaction', true)
                ->first();
        }
    }
}
