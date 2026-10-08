<?php

namespace App\Livewire\Finance\AccountsReceivable;

use App\Models\Invoice;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CreditInvoicesDataTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('receivables.view');
    }

    public function getRows(): Collection
    {
        return Invoice::query()
            ->whereHas('accountsReceivable')
            ->with(['patient', 'accountsReceivable'])
            ->when($this->search, function ($query) {
                $query->where('invoice_number', 'like', "%{$this->search}%")
                    ->orWhereHas('patient', fn ($q) => $q->where('first_name', 'like', "%{$this->search}%"));
            })
            ->when($this->status, fn ($query) => $query->where('payment_status', $this->status))
            ->orderByDesc('issue_date')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.finance.accounts-receivable.credit-invoices-data-table', [
            'invoices' => $this->getRows(),
        ]);
    }
}
