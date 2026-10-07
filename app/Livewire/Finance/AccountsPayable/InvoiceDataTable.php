<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\Finance\SupplierInvoice;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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

    // Payment form fields
    public $amount;

    public $payment_date;

    public $payment_method = 'cash';

    public $reference_number;

    public $transaction_id;

    public $notes;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,check,online,insurance,other',
        'reference_number' => 'nullable|string|max:255',
        'transaction_id' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
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
            $this->amount = $invoice->balance > 0 ? $invoice->balance : null;
            $this->payment_date = now()->format('Y-m-d');
            $this->payment_method = 'cash';
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
        $this->reset(['amount', 'payment_method', 'reference_number', 'transaction_id', 'notes']);
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_method = 'cash';
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

    public function getPaymentMethodsProperty()
    {
        return [
            'cash' => 'Efectivo',
            'credit_card' => 'Tarjeta de Crédito',
            'debit_card' => 'Tarjeta de Débito',
            'bank_transfer' => 'Transferencia Bancaria',
            'check' => 'Cheque',
            'online' => 'Pago Online',
            'insurance' => 'Seguro',
            'other' => 'Otro',
        ];
    }

    public function savePayment(): void
    {
        $this->validate();

        if (! $this->selectedInvoice) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Factura no encontrada.',
            );

            return;
        }

        // Validate amount doesn't exceed balance
        if ($this->amount > $this->selectedInvoice->balance) {
            $this->addError('amount', 'El monto no puede ser mayor al saldo pendiente de B/. '.number_format($this->selectedInvoice->balance, 2));

            return;
        }

        try {

            DB::transaction(function () {
                // Create payment record
                $payment = Payment::create([
                    'amount' => $this->amount,
                    'payment_date' => $this->payment_date,
                    'payment_method' => $this->payment_method,
                    'reference_number' => $this->reference_number,
                    'transaction_id' => $this->transaction_id,
                    'notes' => $this->notes,
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);

                // Update supplier invoice amounts
                $this->selectedInvoice->recordPayment($this->amount);
            });

            $this->dispatch('showToastr',
                type: 'success',
                message: '¡Pago registrado exitosamente!',
            );

            $this->dispatch('paymentSaved');
            $this->closePaymentSchedulingModal();

        } catch (\Exception $e) {
            Log::error('Error saving supplier invoice payment', ['error' => $e->getMessage()]);
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al registrar el pago: '.$e->getMessage(),
            );
        }
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
