<?php

namespace App\Livewire\Finance\AccountsReceivable;

use App\Models\Finance\AccountsReceivable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
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
        try {
            $receivable = AccountsReceivable::find($receivableId);
            if (! $receivable) {
                Log::warning('Receivable not found', ['id' => $receivableId]);

                return;
            }

            $this->authorize('receivables.apply-payment');
            Log::info('Opening payment modal', ['receivable_id' => $receivableId, 'invoice_id' => $receivable->invoice_id]);
            // Dispatch to shared invoice payment modal using invoice ID
            $this->dispatch('openPaymentModal', $receivable->invoice_id);
        } catch (\Exception $e) {
            Log::error('Error opening payment modal', ['error' => $e->getMessage()]);
            $this->dispatch('error', message: 'No tienes permiso para aplicar pagos: '.$e->getMessage());
        }
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
