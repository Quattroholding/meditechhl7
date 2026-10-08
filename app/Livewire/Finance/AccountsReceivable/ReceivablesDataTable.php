<?php

namespace App\Livewire\Finance\AccountsReceivable;

use App\Models\Finance\AccountsReceivable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ReceivablesDataTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public bool $showPaymentModal = false;

    public ?AccountsReceivable $selectedReceivable = null;

    public function mount(): void
    {
        $this->authorize('receivables.view');
    }

    public function getRows(): Collection
    {
        return AccountsReceivable::query()
            ->with(['patient', 'invoice'])
            ->when($this->search, function ($query) {
                $query->where('invoice_number', 'like', "%{$this->search}%")
                    ->orWhereHas('patient', fn ($q) => $q->where('first_name', 'like', "%{$this->search}%"));
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->orderByDesc('invoice_date')
            ->paginate(15);
    }

    public function openPaymentModal(AccountsReceivable $receivable): void
    {
        $this->authorize('receivables.apply-payment');
        $this->selectedReceivable = $receivable;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->selectedReceivable = null;
    }

    public function cancel(AccountsReceivable $receivable): void
    {
        $this->authorize('receivables.manage');
        $receivable->update(['status' => 'cancelled']);
        $this->dispatch('receivable-updated');
    }

    public function render()
    {
        return view('livewire.finance.accounts-receivable.receivables-data-table', [
            'receivables' => $this->getRows(),
        ]);
    }
}
