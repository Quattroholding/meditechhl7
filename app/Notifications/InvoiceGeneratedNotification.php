<?php

namespace App\Notifications;

use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\User;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public ClientInvoice $invoice
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
        $client = $this->invoice->client;
        $subscription = $this->invoice->subscription;

        // Generate PDF
        $pdf = Pdf::loadView('subscriptions.invoices.pdf.invoice', [
            'invoice' => $this->invoice,
            'company' => Client::find(1), // Soluciones Meditec
        ]);
        $pdf->setPaper('letter', 'portrait');

        $pdfContent = $pdf->output();
        $fileName = 'Factura_'.$this->invoice->invoice_number.'_'.now()->format('Y-m-d').'.pdf';

        $mailMessage = (new MailMessage)
            ->subject('Nueva Factura Generada - '.$this->invoice->invoice_number.' - '.$client->name)
            ->view('emails.invoice-generated', [
                'invoice' => $this->invoice,
                'client' => $client,
                'subscription' => $subscription,
            ])
            ->attachData($pdfContent, $fileName, [
                'mime' => 'application/pdf',
            ]);

        // Add CC to accounting email if configured
        $accountingEmail = config('subscriptions.accounting_email');
        if ($accountingEmail) {
            $mailMessage->cc($accountingEmail)->bcc('business@meditecpty.com');
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Standard notification fields
            'title' => 'Nueva Factura Generada',
            'message' => 'Se ha generado la factura '.$this->invoice->invoice_number.' por un monto de $'.number_format($this->invoice->total, 2).' (incluye ITBMS).',
            'steps' => [
                'Período: '.$this->invoice->period_start->format('d/m/Y').' - '.$this->invoice->period_end->format('d/m/Y'),
                'Fecha de vencimiento: '.$this->invoice->due_date->format('d/m/Y'),
                'Plan: '.($this->invoice->subscription->package->name ?? 'N/A'),
                'Subtotal: $'.number_format($this->invoice->subtotal, 2),
                'ITBMS (7%): $'.number_format($this->invoice->tax_amount ?? 0, 2),
            ],
            'action' => [
                'text' => 'Ver Factura',
                'url' => route('suscriptions.invoices.show', $this->invoice->id),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-file-invoice-dollar',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'invoice_generated',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'client_id' => $this->invoice->client_id,
            'client_name' => $this->invoice->client->name,
            'subscription_id' => $this->invoice->subscription_id,
            'package_name' => $this->invoice->subscription->package->name ?? null,
            'subtotal' => $this->invoice->subtotal,
            'discount_amount' => $this->invoice->discount_amount,
            'tax_rate' => $this->invoice->tax_rate ?? 0.07,
            'tax_amount' => $this->invoice->tax_amount ?? 0,
            'total' => $this->invoice->total,
            'due_date' => $this->invoice->due_date->format('Y-m-d'),
            'period_start' => $this->invoice->period_start->format('Y-m-d'),
            'period_end' => $this->invoice->period_end->format('Y-m-d'),
            'status' => $this->invoice->status->value,
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
            'invoice_id' => $this->invoice->id,
            'client_id' => $this->invoice->client_id,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', $context);

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de factura generada', $context);
    }

    /**
     * Get the user to notify (admin client or doctor from the client)
     */
    public static function getNotifiableUser(ClientInvoice $invoice): ?User
    {
        $client = $invoice->client;

        // First try to find an admin client user
        $adminUser = $client->users()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin client');
            })
            ->where('active', true)
            ->first();

        if ($adminUser) {
            return $adminUser;
        }

        // If no admin client, find a doctor
        $doctorUser = $client->users()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'doctor');
            })
            ->where('active', true)
            ->first();

        return $doctorUser;
    }
}
