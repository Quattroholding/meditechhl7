<?php

namespace App\Notifications;

use App\Models\ClientInvoicePayment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public ClientInvoicePayment $payment,
        public string $rejectionReason
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

        $subject = 'Pago Rechazado - '.$invoice->invoice_number.' - '.$client->name;

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->view('emails.payment-rejected', [
                'payment' => $this->payment,
                'invoice' => $invoice,
                'client' => $client,
                'rejectionReason' => $this->rejectionReason,
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

        return [
            // Standard notification fields
            'title' => 'Pago Rechazado',
            'message' => 'Su pago de $'.number_format($this->payment->amount, 2).' para la factura '.$invoice->invoice_number.' ha sido rechazado.',
            'steps' => array_filter([
                'Factura: '.$invoice->invoice_number,
                'Monto: $'.number_format($this->payment->amount, 2),
                'Método: '.$this->payment->payment_method->label(),
                'Referencia: '.$this->payment->payment_reference,
                'Motivo del rechazo: '.$this->rejectionReason,
            ]),
            'action' => [
                'text' => 'Ver Factura',
                'url' => route('suscriptions.invoices.show', $invoice->id),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-times-circle',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'payment_rejected',
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
            'rejection_reason' => $this->rejectionReason,
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
        \Log::error('Falló el envío de notificación de pago rechazado', $context);
    }
}
