<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentModal extends Component
{
    #[Modelable]
    public $showModal = false;
    public $invoice;
    public $invoiceId;

    // Payment form fields
    public $amount;
    public $payment_date;
    public $payment_method = 'cash';
    public $reference_number;
    public $transaction_id;
    public $notes;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,check,online,insurance,other',
        'reference_number' => 'nullable|string|max:255',
        'transaction_id' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'amount.required' => 'El monto es obligatorio.',
        'amount.numeric' => 'El monto debe ser numérico.',
        'amount.min' => 'El monto debe ser mayor a 0.',
        'payment_date.required' => 'La fecha de pago es obligatoria.',
        'payment_date.date' => 'La fecha de pago debe ser una fecha válida.',
        'payment_method.required' => 'El método de pago es obligatorio.',
    ];

    protected $listeners = [
        'openPaymentModal' => 'openModal',
        'closePaymentModal' => 'closeModal',
    ];

    public function mount()
    {
        $this->payment_date = now()->format('Y-m-d');
    }
    #[On('openPaymentModal')]
    public function openModal($invoiceId)
    {
        $this->invoiceId = $invoiceId;
        $this->invoice = Invoice::with(['patient', 'payments'])->find($invoiceId);

        if (!$this->invoice) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Factura no encontrada.'
            ]);
            return;
        }

        // Set default amount to remaining balance
        $this->amount = $this->invoice->balance > 0 ? $this->invoice->balance : null;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['amount', 'payment_method', 'reference_number', 'transaction_id', 'notes']);
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_method = 'cash';
    }

    public function savePayment()
    {
        $this->validate();

        if (!$this->invoice) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Factura no encontrada.'
            ]);
            return;
        }

        // Validate amount doesn't exceed balance
        if ($this->amount > $this->invoice->balance) {
            $this->addError('amount', 'El monto no puede ser mayor al saldo pendiente de $'.number_format($this->invoice->balance, 2));
            return;
        }

        try {
            DB::transaction(function () {
                // Create payment record
                $payment = Payment::create([
                    'invoice_id' => $this->invoice->id,
                    'patient_id' => $this->invoice->patient_id,
                    'amount' => $this->amount,
                    'payment_date' => $this->payment_date,
                    'payment_method' => $this->payment_method,
                    'reference_number' => $this->reference_number,
                    'transaction_id' => $this->transaction_id,
                    'notes' => $this->notes,
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);

                // Update invoice amounts
                $this->invoice->amount_paid = ($this->invoice->amount_paid ?? 0) + $this->amount;
                $this->invoice->amount_due = $this->invoice->total_net - $this->invoice->amount_paid;

                // Update payment status
                if ($this->invoice->amount_due <= 0.01) {
                    $this->invoice->payment_status = 'paid';
                    $this->invoice->payment_date = $this->payment_date;
                } else {
                    $this->invoice->payment_status = 'partial';
                }

                $this->invoice->updated_by = auth()->id();
                $this->invoice->save();
            });

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => '¡Pago registrado exitosamente!'
            ]);

            $this->dispatch('paymentSaved');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Error al registrar el pago: '.$e->getMessage()
            ]);
        }
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

    public function render()
    {
        return view('livewire.invoice.payment-modal');
    }
}
