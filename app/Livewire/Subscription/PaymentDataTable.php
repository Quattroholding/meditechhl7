<?php

namespace App\Livewire\Subscription;

use App\Models\ClientInvoicePayment;
use App\Notifications\PaymentRejectedNotification;
use Illuminate\Pagination\LengthAwarePaginator;
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

    // Reject modal properties
    public $showRejectModal = false;

    public $rejectPaymentId = null;

    public $rejectReason = '';

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

    public function openRejectModal($paymentId)
    {
        $this->rejectPaymentId = $paymentId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectPaymentId = null;
        $this->rejectReason = '';
        $this->resetValidation();
    }

    public function rejectPayment()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5|max:1000',
        ], [
            'rejectReason.required' => 'Debe ingresar un motivo para rechazar el pago.',
            'rejectReason.min' => 'El motivo debe tener al menos 10 caracteres.',
            'rejectReason.max' => 'El motivo no puede tener más de 1000 caracteres.',
        ]);

        try {
            $payment = ClientInvoicePayment::with(['invoice.client', 'invoice.subscription.package'])
                ->findOrFail($this->rejectPaymentId);

            // Only allow rejection of pending payments
            if ($payment->status->value !== 'pending') {
                $this->dispatch('showRejectdPaymentToastr', [
                    'type' => 'error',
                    'message' => 'Solo se pueden rechazar pagos pendientes.',
                ]);

                $this->closeRejectModal();

                return;
            }

            $payment->markAsFailed($this->rejectReason, auth()->id());

            // Send notification to client about payment rejection
            $this->notifyClientAboutRejection($payment);

            $this->dispatch('showRejectdPaymentToastr', [
                'type' => 'success',
                'message' => 'Pago rechazado exitosamente.',
            ]);

            $this->closeRejectModal();
        } catch (\Exception $e) {
            $this->dispatch('showRejectdPaymentToastr', [
                'type' => 'error',
                'message' => 'Error al rechazar el pago: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Notify client about payment rejection
     */
    protected function notifyClientAboutRejection(ClientInvoicePayment $payment): void
    {
        try {
            $client = $payment->invoice->client;

            // Find the client's admin or doctor user to notify
            $clientUser = $client->users()
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin client', 'doctor']);
                })
                ->where('active', true)
                ->first();

            if ($clientUser) {
                $clientUser->notify(new PaymentRejectedNotification($payment, $this->rejectReason));

                \Log::info('Notificación de pago rechazado enviada al cliente', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->client_invoice_id,
                    'client_id' => $client->id,
                    'user_id' => $clientUser->id,
                    'rejection_reason' => $this->rejectReason,
                ]);
            } else {
                \Log::warning('No se encontró usuario del cliente para notificar sobre el pago rechazado', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->client_invoice_id,
                    'client_id' => $client->id,
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the payment rejection
            \Log::error('Error al enviar notificación de pago rechazado al cliente', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function verifyPayment($paymentId)
    {
        try {
            $payment = ClientInvoicePayment::findOrFail($paymentId);

            // Only allow verification of pending payments
            if ($payment->status->value !== 'pending') {
                $this->dispatch('showRejectdPaymentToastr', [
                    'type' => 'error',
                    'message' => 'Solo se pueden verificar pagos pendientes.',
                ]);

                return;
            }

            $payment->markAsCompleted(auth()->id());

            $this->dispatch('showRejectdPaymentToastr', [
                'type' => 'success',
                'message' => 'Pago verificado y aprobado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('showRejectdPaymentToastr', [
                'type' => 'error',
                'message' => 'Error al verificar el pago: '.$e->getMessage(),
            ]);
        }
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
                'payments' => new LengthAwarePaginator([], 0, 10),
            ]);
        }
    }
}
