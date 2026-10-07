<?php

namespace App\Livewire\Finance\AccountsReceivable;

use App\Models\Finance\AccountsReceivable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'due_date';

    public $sortDirection = 'asc';

    public $pagination = 10;

    public $statusFilter = 'all'; // all, pending, partial, paid, overdue, cancelled

    public bool $showPaymentModal = false;

    public ?AccountsReceivable $selectedReceivable = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function applyPayment(int $receivableId): void
    {
        $receivable = AccountsReceivable::find($receivableId);
        if ($receivable) {
            $this->openPaymentModal($receivable);
        }
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

    #[On('closePaymentModal')]
    public function handleClosePaymentModal(): void
    {
        $this->closePaymentModal();
    }

    #[On('payment-applied')]
    public function handlePaymentApplied(): void
    {
        $this->closePaymentModal();
        // Opcional: refrescar los datos
        $this->dispatch('receivable-updated');
    }

    public function render(): View
    {
        $data = AccountsReceivable::query()
            ->with(['patient', 'invoice', 'branch'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('patient', function ($q2) {
                            $q2->where('first_name', 'like', '%'.$this->search.'%')
                                ->orWhere('last_name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.finance.accounts-receivable.data-table', [
            'data' => $data,
        ]);
    }
}
