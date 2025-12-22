<?php

namespace App\Livewire\Subscription;

use App\Models\ClientInvoicePayment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentDataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $methodFilter = '';

    public $perPage = 10;

    public $sortField = 'payment_date';

    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'methodFilter' => ['except' => ''],
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

    public function updatingMethodFilter()
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

    public function getPaymentsProperty()
    {
        return ClientInvoicePayment::query()
            ->with(['invoice.client', 'invoice.subscription.package', 'processedBy'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_reference', 'like', '%'.$this->search.'%')
                        ->orWhere('gateway_transaction_id', 'like', '%'.$this->search.'%')
                        ->orWhereHas('invoice', function ($invoiceQuery) {
                            $invoiceQuery->where('invoice_number', 'like', '%'.$this->search.'%')
                                ->orWhereHas('client', function ($clientQuery) {
                                    $clientQuery->where('name', 'like', '%'.$this->search.'%');
                                });
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->methodFilter, function ($query) {
                $query->where('payment_method', $this->methodFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        try {
            $payments = $this->payments;

            return view('livewire.subscription.payment-data-table', [
                'payments' => $payments,
            ]);
        } catch (\Exception $e) {
            \Log::error('Subscription Payment DataTable Error: '.$e->getMessage());

            return view('livewire.subscription.payment-data-table', [
                'payments' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
            ]);
        }
    }
}
