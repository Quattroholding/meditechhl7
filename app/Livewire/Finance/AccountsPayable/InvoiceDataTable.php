<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\Finance\SupplierInvoice;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
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

    public bool $showPaymentSchedulingModal = false;

    public ?SupplierInvoice $selectedInvoice = null;

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

    public function schedulePayment(int $invoiceId): void
    {
        try {
            $invoice = SupplierInvoice::find($invoiceId);
            if (! $invoice) {
                Log::warning('Supplier invoice not found', ['id' => $invoiceId]);

                return;
            }

            $this->authorize('payables.payments.process');
            $this->selectedInvoice = $invoice;
            $this->showPaymentSchedulingModal = true;
            Log::info('Payment scheduling modal opened', ['invoice_id' => $invoiceId]);
        } catch (\Exception $e) {
            Log::error('Error opening payment scheduling modal', ['error' => $e->getMessage()]);
            $this->dispatch('error', message: 'No tienes permiso para programar pagos: '.$e->getMessage());
        }
    }

    public function closePaymentSchedulingModal(): void
    {
        $this->showPaymentSchedulingModal = false;
        $this->selectedInvoice = null;
    }

    #[On('closePaymentSchedulingModal')]
    public function handleClosePaymentSchedulingModal(): void
    {
        $this->closePaymentSchedulingModal();
    }

    #[On('payment-scheduled')]
    public function handlePaymentScheduled(): void
    {
        $this->closePaymentSchedulingModal();
        $this->dispatch('invoice-updated');
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
