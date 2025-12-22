<?php

namespace App\Livewire\Subscription;

use App\Models\ClientInvoice;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceDataTable extends Component
{
    use WithPagination;

    protected $listeners = ['paymentSaved' => '$refresh'];

    public $search = '';

    public $statusFilter = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

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

    public function getInvoicesProperty()
    {
        return ClientInvoice::query()
            ->with(['client', 'subscription.package', 'payments'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('client', function ($clientQuery) {
                            $clientQuery->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        try {
            $invoices = $this->invoices;

            return view('livewire.subscription.invoice-data-table', [
                'invoices' => $invoices,
            ]);
        } catch (\Exception $e) {
            \Log::error('Subscription Invoice DataTable Error: '.$e->getMessage());

            return view('livewire.subscription.invoice-data-table', [
                'invoices' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
            ]);
        }
    }
}
