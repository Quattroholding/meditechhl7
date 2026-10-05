<?php

namespace App\Livewire\Accounting\JournalEntry;

use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\Finance\CostCenter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class JournalEntryModal extends Component
{
    public ?JournalEntry $entry = null;

    #[Validate]
    public string $entry_date = '';

    #[Validate]
    public string $document_type = '';

    #[Validate]
    public string $document_number = '';

    #[Validate]
    public string $description = '';

    public array $lines = [];

    public float $totalDebit = 0;

    public float $totalCredit = 0;

    public bool $isBalanced = false;

    public function mount(?JournalEntry $entry = null): void
    {
        if ($entry) {
            $this->entry = $entry;
            $this->entry_date = $entry->entry_date->toDateString();
            $this->document_type = $entry->document_type;
            $this->document_number = $entry->document_number;
            $this->description = $entry->description;

            $this->lines = $entry->journalEntryLines->map(fn ($line) => [
                'id' => $line->id,
                'accounting_account_id' => $line->accounting_account_id,
                'cost_center_id' => $line->cost_center_id,
                'debit' => $line->debit,
                'credit' => $line->credit,
                'description' => $line->description,
            ])->toArray();
        } else {
            $this->entry_date = now()->toDateString();
            $this->addLine();
        }
        $this->calculateTotals();
    }

    public function rules(): array
    {
        return [
            'entry_date' => 'required|date',
            'document_type' => 'required|string|max:50',
            'document_number' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'lines' => 'required|array|min:2',
            'lines.*.accounting_account_id' => 'required|exists:accounting_accounts,id',
            'lines.*.cost_center_id' => 'nullable|exists:cost_centers,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:255',
        ];
    }

    public function save(): void
    {
        $this->authorize('accounting.entries.manage');
        $this->validate();

        // Validar que las líneas estén balanceadas
        if (! $this->isBalancedValidation()) {
            $this->dispatch('swal:alert', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Los débitos y créditos no están balanceados.',
            ]);

            return;
        }

        $clientId = auth()->user()->getCurrentClient()->id;

        if ($this->entry) {
            // Actualizar entrada existente
            $this->entry->update([
                'entry_date' => $this->entry_date,
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'description' => $this->description,
                'updated_by' => auth()->id(),
            ]);

            // Eliminar líneas anteriores
            $this->entry->journalEntryLines()->delete();
        } else {
            // Crear nueva entrada
            $this->entry = JournalEntry::create([
                'client_id' => $clientId,
                'entry_date' => $this->entry_date,
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'description' => $this->description,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);
        }

        // Crear nuevas líneas
        foreach ($this->lines as $lineData) {
            if ($lineData['debit'] > 0 || $lineData['credit'] > 0) {
                JournalEntryLine::create([
                    'journal_entry_id' => $this->entry->id,
                    'accounting_account_id' => $lineData['accounting_account_id'],
                    'cost_center_id' => $lineData['cost_center_id'],
                    'debit' => $lineData['debit'],
                    'credit' => $lineData['credit'],
                    'description' => $lineData['description'],
                ]);
            }
        }

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'title' => 'Éxito',
            'text' => $this->entry ? 'Asiento actualizado exitosamente.' : 'Asiento creado exitosamente.',
        ]);

        $this->dispatch('entry-saved');
        $this->dispatch('closeModal');
    }

    public function delete(): void
    {
        if ($this->entry && $this->entry->isDraft()) {
            $this->authorize('accounting.entries.manage');
            $this->entry->delete();
            $this->dispatch('entry-saved');
            $this->dispatch('closeModal');
        }
    }

    public function addLine(): void
    {
        $this->lines[] = [
            'id' => null,
            'accounting_account_id' => 0,
            'cost_center_id' => null,
            'debit' => 0,
            'credit' => 0,
            'description' => '',
        ];
    }

    public function removeLine(int $index): void
    {
        if (count($this->lines) > 2) {
            unset($this->lines[$index]);
            $this->lines = array_values($this->lines);
        }
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->totalDebit = array_sum(array_column($this->lines, 'debit'));
        $this->totalCredit = array_sum(array_column($this->lines, 'credit'));
        $this->isBalanced = abs($this->totalDebit - $this->totalCredit) < 0.01;
    }

    public function updatedLines(): void
    {
        $this->calculateTotals();
    }

    public function isBalancedValidation(): bool
    {
        return abs($this->totalDebit - $this->totalCredit) < 0.01;
    }

    public function getAccountingAccountsProperty()
    {
        return AccountingAccount::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->where('allows_transaction', true)
            ->orderBy('code')
            ->get();
    }

    public function getCostCentersProperty()
    {
        return CostCenter::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.accounting.journal-entry.journal-entry-modal', [
            'accounts' => $this->accountingAccounts,
            'costCenters' => $this->costCenters,
        ]);
    }
}
