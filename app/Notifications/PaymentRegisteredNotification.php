<?php

namespace App\Notifications;

use App\Models\ClientInvoicePayment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public ClientInvoicePayment $payment
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return array_filter([
            'database',
            $this->getMailChannelIfValid($notifiable->email),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $invoice = $this->payment->invoice;
        $client = $invoice->client;
        $registeredBy = $this->payment->processedBy;

        $subject = 'Nuevo Pago Registrado - '.$invoice->invoice_number.' - '.$client->name;

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->view('emails.payment-registered', [
                'payment' => $this->payment,
                'invoice' => $invoice,
                'client' => $client,
                'registeredBy' => $registeredBy,
            ]);

        $mailMessage->bcc('business@meditecpty.com');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $invoice = $this->payment->invoice;
        $client = $invoice->client;
        $registeredBy = $this->payment->processedBy;

        return [
            // Standard notification fields
            'title' => 'Nuevo Pago Registrado',
            'message' => 'Se ha registrado un pago de $'.number_format($this->payment->amount, 2).' para la factura '.$invoice->invoice_number.' del cliente '.$client->name.'.',
            'steps' => array_filter([
                'Cliente: '.$client->name,
                'Factura: '.$invoice->invoice_number,
                'Monto: $'.number_format($this->payment->amount, 2),
                'Método: '.$this->payment->payment_method->label(),
                'Referencia: '.$this->payment->payment_reference,
                'Estado: '.$this->payment->status->label(),
                'Registrado por: '.$registeredBy->full_name,
            ]),
            'action' => [
                'text' => 'Ver Pago',
                'url' => route('suscriptions.payments.show', $this->payment->id),
            ],
            'priority' => $this->payment->status->value === 'pending' ? 'high' : 'medium',
            'icon' => 'fas fa-credit-card',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'payment_registered',
            'payment_id' => $this->payment->id,
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'client_id' => $client->id,
            'client_name' => $client->name,
            'amount' => $this->payment->amount,
            'payment_method' => $this->payment->payment_method->value,
            'payment_method_label' => $this->payment->payment_method->label(),
            'payment_reference' => $this->payment->payment_reference,
            'payment_date' => $this->payment->payment_date->format('Y-m-d'),
            'status' => $this->payment->status->value,
            'status_label' => $this->payment->status->label(),
            'has_receipt' => ! empty($this->payment->receipt_file_path),
            'receipt_file_path' => $this->payment->receipt_file_path,
            'registered_by_id' => $registeredBy->id,
            'registered_by_name' => $registeredBy->full_name,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $context = [
            'payment_id' => $this->payment->id,
            'invoice_id' => $this->payment->client_invoice_id,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', $context);

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de pago registrado', $context);
    }
}
