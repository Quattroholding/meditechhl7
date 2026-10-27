<?php

namespace App\Livewire\Accounting\JournalEntry;

use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Finance\CostCenter;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LineForm extends Component
{
    public JournalEntry $journalEntry;

    public JournalEntryLine $line;

    public $accountingAccount = null;

    public $costCenter = null;

    public $debit = null;

    public $credit = null;

    public $description = null;

    protected function rules(): array
    {
        return [
            'accountingAccount' => 'required|exists:accounting_accounts,id',
            'debit' => 'nullable|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'costCenter' => 'nullable|exists:cost_centers,id',
        ];
    }

    public function mount(JournalEntry $journalEntry, JournalEntryLine $line)
    {
        $this->journalEntry = $journalEntry;
        $this->line = $line;
        $this->accountingAccount = $line->accounting_account_id;
        $this->costCenter = $line->cost_center_id;
        $this->debit = $line->debit;
        $this->credit = $line->credit;
        $this->description = $line->description;
    }

    public function save()
    {
        $this->validate();

        if ($this->debit > 0 && $this->credit > 0) {
            $this->addError('debit', 'No se puede tener débito y crédito en la misma línea');

            return;
        }

        if ($this->debit == 0 && $this->credit == 0) {
            $this->addError('debit', 'Debe especificar un monto en débito o crédito');

            return;
        }

        $this->line->update([
            'accounting_account_id' => $this->accountingAccount,
            'cost_center_id' => $this->costCenter,
            'debit' => $this->debit ?? 0,
            'credit' => $this->credit ?? 0,
            'description' => $this->description,
        ]);

        $this->dispatch('lineUpdated');
        $this->dispatch('closeLineForm');
        $this->dispatch('showToastrJournalEntryEdit', type: 'success', message: 'Línea actualizada correctamente');
    }

    public function delete()
    {
        $this->authorize('accounting.entries.edit');

        if (! $this->journalEntry->accountingPeriod->canModify()) {
            throw new \Exception('El período no está abierto');
        }

        $this->line->delete();
        $this->dispatch('lineDeleted');
        $this->dispatch('closeLineForm');
        $this->dispatch('showToastrJournalEntryEdit', type: 'success', message: 'Línea eliminada correctamente');
    }

    #[Computed]
    public function accountingAccounts()
    {
        return AccountingAccount::where('client_id', $this->journalEntry->client_id)
            ->orderBy('code')
            ->get();
    }

    #[Computed]
    public function costCenters()
    {
        return CostCenter::where('client_id', $this->journalEntry->client_id)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.accounting.journal-entry.line-form');
    }
}
