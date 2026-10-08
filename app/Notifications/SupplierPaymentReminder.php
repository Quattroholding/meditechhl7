<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierPaymentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $overduePayments,
        public float $totalDue,
        public int $paymentCount,
        public string $clientName = ''
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('⏰ Recordatorio: Pagos Vencidos a Proveedores')
            ->greeting("Hola {$notifiable->name},")
            ->line("Se han detectado **{$this->paymentCount} pagos vencidos** por un total de **B/. ".number_format($this->totalDue, 2)."** en {$this->clientName}.")
            ->line('')
            ->line('**Detalle de Pagos Vencidos:**')
            ->line('');

        // Agregar cada pago vencido
        foreach ($this->overduePayments as $payment) {
            $daysOverdue = $payment['days_overdue'];
            $daysText = $daysOverdue == 1 ? 'día' : 'días';

            $mail->line(sprintf(
                '• **%s** - Proveedor: %s | Monto: B/. %s | Vencido: %d %s',
                $payment['date'],
                $payment['supplier'],
                number_format($payment['amount'], 2),
                $daysOverdue,
                $daysText
            ));
        }

        $mail->line('')
            ->line('Por favor, procede a registrar estos pagos lo antes posible.')
            ->action('Ver Cuentas por Pagar', route('finance.payables.invoices.index'))
            ->line('Este es un recordatorio automático del sistema de gestión financiera.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_count' => $this->paymentCount,
            'total_due' => $this->totalDue,
            'client_name' => $this->clientName,
        ];
    }
}
