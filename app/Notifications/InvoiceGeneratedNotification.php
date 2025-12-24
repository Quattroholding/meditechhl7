<?php

namespace App\Notifications;

use App\Models\ClientInvoice;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public ClientInvoice $invoice
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
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
            'company' => \App\Models\Client::find(1), // Soluciones Meditec
        ]);
        $pdf->setPaper('letter', 'portrait');

        $pdfContent = $pdf->output();
        $fileName = 'Factura_'.$this->invoice->invoice_number.'_'.now()->format('Y-m-d').'.pdf';

        $mailMessage = (new MailMessage)
            ->subject('Nueva Factura Generada - '.$this->invoice->invoice_number.' - '.$client->name)
            ->greeting('¡Hola!')
            ->line('Se ha generado una nueva factura para su suscripción de **'.$subscription->package->name.'**.')
            ->line('**Número de Factura:** '.$this->invoice->invoice_number)
            ->line('**Período:** '.$this->invoice->period_start->format('d/m/Y').' - '.$this->invoice->period_end->format('d/m/Y'))
            ->line('**Total a pagar:** $'.number_format($this->invoice->total, 2))
            ->line('**Fecha de vencimiento:** '.$this->invoice->due_date->format('d/m/Y'))
            ->line('')
            ->line('### Métodos de Pago Disponibles:')
            ->line('')
            ->line('**1. ACH - Transferencia Bancaria**')
            ->line('- **Banco:** Banco General')
            ->line('- **Cuenta:** 04-99-99-999999-9')
            ->line('- **Tipo:** Cuenta Corriente')
            ->line('- **Beneficiario:** Meditech S.A.')
            ->line('')
            ->line('**2. YAPPY**')
            ->line('- **Número:** 6XXX-XXXX')
            ->line('- **Nombre:** Meditech')
            ->line('- **Código YAPPY:** '.$client->yappy_code)
            ->line('- **IMPORTANTE:** En el mensaje de la transferencia YAPPY incluya su código ('.$client->yappy_code.') para identificar el pago.')
            ->line('')
            ->line('### Registro de Pago:')
            ->line('Puede registrar su pago manualmente en la plataforma adjuntando la captura de pantalla de su transacción para una aprobación más rápida.')
            ->line('Para registrar el pago, acceda a **Suscripciones > Facturas** y haga clic en el ícono azul con el símbolo de tarjeta de crédito.')
            ->action('Ver Factura', route('suscriptions.invoices.show', $this->invoice->id))
            ->line('¡Gracias por confiar en nosotros!')
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
            'type' => 'invoice_generated',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'client_id' => $this->invoice->client_id,
            'client_name' => $this->invoice->client->name,
            'subscription_id' => $this->invoice->subscription_id,
            'package_name' => $this->invoice->subscription->package->name ?? null,
            'total' => $this->invoice->total,
            'due_date' => $this->invoice->due_date->format('Y-m-d'),
            'period_start' => $this->invoice->period_start->format('Y-m-d'),
            'period_end' => $this->invoice->period_end->format('Y-m-d'),
            'status' => $this->invoice->status->value,
            'message' => 'Se ha generado una nueva factura por $'.number_format($this->invoice->total, 2).'. Vence el '.$this->invoice->due_date->format('d/m/Y').'.',
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Falló el envío de notificación de factura generada', [
            'invoice_id' => $this->invoice->id,
            'client_id' => $this->invoice->client_id,
            'error' => $exception->getMessage(),
        ]);
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
