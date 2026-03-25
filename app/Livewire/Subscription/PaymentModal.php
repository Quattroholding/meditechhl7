<?php

namespace App\Livewire\Subscription;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\ClientInvoice;
use App\Models\ClientInvoicePayment;
use App\Models\User;
use App\Notifications\PaymentRegisteredNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaymentModal extends Component
{
    use WithFileUploads;

    #[Modelable]
    public $showModal = false;

    public $invoice;

    public $invoiceId;

    // Payment form fields
    public $amount;

    public $payment_date;

    public $payment_method = 'ACH';

    public $payment_reference;

    public $gateway_transaction_id;

    public $payment_gateway;

    public $notes;

    public $receipt_file;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:ACH,YAPPY',
        'payment_reference' => 'required|string|max:255',
        'gateway_transaction_id' => 'nullable|string|max:255',
        'payment_gateway' => 'nullable|string|max:100',
        'notes' => 'nullable|string|max:1000',
        'receipt_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ];

    protected $messages = [
        'amount.required' => 'El monto es obligatorio.',
        'receipt_file.required' => 'Debe agregar el comprobante de pago.',
        'amount.numeric' => 'El monto debe ser numérico.',
        'amount.min' => 'El monto debe ser mayor a 0.',
        'payment_date.required' => 'La fecha de pago es obligatoria.',
        'payment_date.date' => 'La fecha de pago debe ser una fecha válida.',
        'payment_method.required' => 'El método de pago es obligatorio.',
        'receipt_file.file' => 'El comprobante debe ser un archivo.',
        'receipt_file.mimes' => 'El comprobante debe ser PDF, JPG, JPEG o PNG.',
        'receipt_file.max' => 'El comprobante no debe pesar más de 5MB.',
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
        $this->invoice = ClientInvoice::with(['client', 'subscription.package', 'payments'])->find($invoiceId);

        if (! $this->invoice) {
            $this->dispatch('showToastrSubscriptionPaymentModal',
                type : 'error',
                message : 'Factura no encontrada.',
            );

            return;
        }

        // Set default amount to remaining balance
        $this->amount = $this->invoice->amount_due > 0 ? $this->invoice->amount_due : null;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            'amount',
            'payment_method',
            'payment_reference',
            'gateway_transaction_id',
            'payment_gateway',
            'notes',
            'receipt_file',
        ]);
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_method = 'ACH';
    }

    public function savePayment()
    {
        $this->validate();

        if (! $this->invoice) {
            $this->dispatch('showToastrSubscriptionPaymentModal',
                type: 'error',
                message : 'Factura no encontrada.',
            );

            session()->flash('message.error', 'Factura no encontrada.');

            return;
        }

        // Validate amount doesn't exceed balance
        if ($this->amount > $this->invoice->amount_due) {
            $this->addError('amount', 'El monto no puede ser mayor al saldo pendiente de $'.number_format($this->invoice->amount_due, 2));

            return;
        }

        try {
            $payment = null;

            DB::transaction(function () use (&$payment) {
                // Handle file upload if present
                $receiptFilePath = null;
                if ($this->receipt_file) {
                    $filename = 'payment_'.time().'_'.$this->invoice->invoice_number.'.'.$this->receipt_file->extension();
                    $receiptFilePath = $this->receipt_file->storeAs('payment-receipts', $filename);
                }

                $status = PaymentStatus::PENDING;
                if (auth()->user()->hasRole('contabilidad')) {
                    $status = PaymentStatus::COMPLETED;
                }

                // Create payment record
                $payment = ClientInvoicePayment::create([
                    'client_invoice_id' => $this->invoice->id,
                    'amount' => $this->amount,
                    'payment_date' => $this->payment_date,
                    'payment_method' => PaymentMethod::from($this->payment_method),
                    'payment_reference' => $this->payment_reference,
                    'gateway_transaction_id' => $this->gateway_transaction_id,
                    'payment_gateway' => $this->payment_gateway,
                    'notes' => $this->notes,
                    'receipt_file_path' => $receiptFilePath,
                    'status' => $status,
                    'processed_by' => auth()->id(),
                ]);

                // Update invoice payment status
                $this->invoice->updatePaymentStatus();
            });

            // Send notification to accounting department
            if ($payment) {
                $this->notifyAccountingDepartment($payment);
            }

            $this->dispatch('showToastrSubscriptionPaymentModal',
                type : 'success',
                message : '¡Pago registrado exitosamente!',
            );

            session()->flash('message.success', '¡Pago registrado exitosamente!');
            $this->dispatch('paymentSaved');
            $this->dispatch('refreshSetupReminders');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('showToastrSubscriptionPaymentModal',
                type : 'error',
                message : 'Error al registrar el pago: '.$e->getMessage(),
            );
            session()->flash('message.error', 'Error al registrar el pago: '.$e->getMessage());
        }
    }

    /**
     * Notify accounting department about new payment registration
     */
    protected function notifyAccountingDepartment(ClientInvoicePayment $payment): void
    {
        try {
            // Load payment with necessary relationships
            $payment->load(['invoice.client', 'invoice.subscription.package', 'processedBy']);

            // Get all active users with 'contabilidad' role
            $accountingUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'contabilidad');
            })
                ->where('active', true)
                ->get();

            // Send notification to all accounting users
            if ($accountingUsers->isNotEmpty()) {
                Notification::send($accountingUsers, new PaymentRegisteredNotification($payment));

                \Log::info('Notificación de pago registrado enviada al departamento de contabilidad', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->client_invoice_id,
                    'amount' => $payment->amount,
                    'accounting_users_count' => $accountingUsers->count(),
                ]);
            } else {
                \Log::warning('No se encontraron usuarios de contabilidad para notificar sobre el pago', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->client_invoice_id,
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the payment registration
            \Log::error('Error al enviar notificación de pago a contabilidad', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function getPaymentMethodsProperty()
    {
        $methods = [];
        foreach (PaymentMethod::cases() as $method) {
            $methods[$method->value] = $method->label();
        }

        return $methods;
    }

    public function render()
    {
        return view('livewire.subscription.payment-modal');
    }
}
