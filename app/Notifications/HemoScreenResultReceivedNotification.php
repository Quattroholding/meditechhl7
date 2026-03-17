<?php

namespace App\Notifications;

use App\Models\HemoScreenStandaloneResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HemoScreenResultReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public HemoScreenStandaloneResult $result) {}

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
        $abnormalObs = $this->result->getAbnormalObservations();
        $hasAbnormal = count($abnormalObs) > 0;

        $message = (new MailMessage)
            ->subject('Nuevos resultados HemoScreen - '.now()->format('d/m/Y H:i'))
            ->greeting('Hola '.$notifiable->first_name.',')
            ->line('Se han recibido nuevos resultados de tu dispositivo HemoScreen.');

        $message->line('**Información del test:**')
            ->line('• Fecha: '.$this->result->test_performed_at->format('d/m/Y H:i'))
            ->line('• Dispositivo: '.$this->result->device_serial)
            ->line('• Panel: '.$this->result->panel_name.' (CPT '.$this->result->panel_code.')');

        if ($hasAbnormal) {
            $message->line('⚠️ **Valores anormales detectados:**');

            foreach ($abnormalObs as $obs) {
                $message->line("• {$obs['name']}: {$obs['value']} {$obs['unit']}");
            }
        } else {
            $message->line('✓ Todos los valores están dentro del rango de referencia normal.');
        }

        $message->action('Ver resultados completos', url('/hemoscreen/dashboard'));

        $message->line('Gracias por usar SAMI HemoScreen.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'result_id' => $this->result->id,
            'test_performed_at' => $this->result->test_performed_at->toIso8601String(),
            'device_serial' => $this->result->device_serial,
            'has_abnormal' => $this->result->hasAbnormalValues(),
        ];
    }
}
