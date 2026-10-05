<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\Finance\SupplierInvoice;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceDataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'invoice_date';

    public $sortDirection = 'desc';

    public $pagination = 10;

    public $statusFilter = 'all'; // all, draft, registered, approved, partial, paid, overdue, cancelled

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

    public function render(): View
    {
        $data = SupplierInvoice::query()
            ->with(['supplier', 'costCenter'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('supplier', function ($q2) {
                            $q2->where('legal_name', 'like', '%'.$this->search.'%')
                                ->orWhere('commercial_name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.finance.accounts-payable.invoice-data-table', [
            'data' => $data,
        ]);
    }
}
