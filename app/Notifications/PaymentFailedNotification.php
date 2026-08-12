<?php

namespace App\Notifications;

use App\Enums\NeoPaymentStatus;
use App\Enums\PaymentMethod;
use App\Models\ClientInvoice;
use App\Models\ClientInvoicePayment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public ClientInvoice $invoice,
        public ClientInvoicePayment $payment,
        public NeoPaymentStatus $status
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
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        $client = $this->invoice->client;

        return [
            'Type' => 'payment-failed',
            'Payment-ID' => $this->payment->id,
            'Invoice-ID' => $this->invoice->id,
            'Invoice-Number' => $this->invoice->invoice_number,
            'Client-ID' => $client->id,
            'Client-Name' => $client->name,
            'Amount' => number_format($this->invoice->total, 2),
            'Failure-Status' => $this->status->value,
            'Failure-Reason' => $this->getFailureReason(),
            'Due-Date' => $this->invoice->due_date->format('Y-m-d'),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $client = $this->invoice->client;
        $failureReason = $this->getFailureReason();

        return (new MailMessage)
            ->subject('Pago de Factura Fallido - '.$this->invoice->invoice_number.' - '.$client->name)
            ->line('El pago automático de la factura '.$this->invoice->invoice_number.' no pudo ser procesado.')
            ->line('**Razón:** '.$failureReason)
            ->line('**Monto:** $'.number_format($this->invoice->total, 2))
            ->line('**Fecha de vencimiento:** '.$this->invoice->due_date->format('d/m/Y'))
            ->line('')
            ->line('**Métodos de pago alternativos disponibles:**')
            ->line('- **ACH**: '.PaymentMethod::ACH->info()['Banco'].' - Cuenta: '.PaymentMethod::ACH->info()['Cuenta'])
            ->line('- **Yappy**: '.PaymentMethod::YAPPY->info()['Teléfono'])
            ->line('')
            ->action('Ver Factura y Pagar', route('suscriptions.invoices.show', $this->invoice->id))
            ->line('Por favor, realice el pago antes de la fecha de vencimiento para evitar la suspensión del servicio.')
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pago de Factura Fallido',
            'message' => 'El pago automático de la factura '.$this->invoice->invoice_number.' por $'.number_format($this->invoice->total, 2).' no pudo ser procesado.',
            'steps' => [
                'Razón: '.$this->getFailureReason(),
                'Fecha de vencimiento: '.$this->invoice->due_date->format('d/m/Y'),
                'Métodos de pago disponibles: ACH, Yappy',
            ],
            'action' => [
                'text' => 'Ver Factura',
                'url' => route('suscriptions.invoices.show', $this->invoice->id),
            ],
            'priority' => 'urgent',
            'icon' => 'fas fa-exclamation-triangle',
            'invoice_id' => $this->invoice->id,
            'payment_id' => $this->payment->id,
            'failure_status' => $this->status->value,
        ];
    }

    private function getFailureReason(): string
    {
        return match ($this->status) {
            NeoPaymentStatus::DENIED => 'Transacción denegada por el banco',
            NeoPaymentStatus::REFUSED => 'Transacción rechazada por el sistema',
            NeoPaymentStatus::FAILED => 'Error al procesar el pago',
            default => 'No se pudo completar el pago',
        };
    }
}
