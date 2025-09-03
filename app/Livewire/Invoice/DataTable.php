<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $perPage = 5;

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
        return Invoice::query()
            ->with(['patient', 'encounter', 'performerPractitioner', 'client'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('patient', function ($patientQuery) {
                            $patientQuery->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('given_name', 'like', '%'.$this->search.'%')
                                ->orWhere('family_name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function markAsPaid($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoice->update([
                'payment_status' => 'paid',
                'amount_paid' => $invoice->total_amount,
                'amount_due' => 0,
                'payment_date' => now(),
            ]);

            session()->flash('message', __('invoice.invoice_marked_paid'));
        }
    }

    public function markAsSent($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoice->update(['status' => 'sent']);
            session()->flash('message', __('invoice.invoice_marked_sent'));
        }
    }

    public function deleteInvoice($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoice->delete();
            session()->flash('message', __('invoice.invoice_deleted'));
        }
    }

    public function render()
    {
        try {
            $invoices = $this->invoices;

            return view('livewire.invoice.data-table', [
                'invoices' => $invoices,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Invoice DataTable Error: '.$e->getMessage());

            // Return an empty collection to prevent infinite loading
            return view('livewire.invoice.data-table', [
                'invoices' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
            ]);
        }
    }

    public function openPaymentModal($invoiceId)
    {
        $this->dispatch('openPaymentModal', $invoiceId);
    }

    #[On('paymentSaved')]
    public function paymentSaved()
    {

        dd('aqui');
        $invoices = $this->invoices;
    }
}
