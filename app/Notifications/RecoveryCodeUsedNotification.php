<?php

namespace App\Notifications;

use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecoveryCodeUsedNotification extends Notification implements ShouldQueue
{
    use Queueable, WithEmailMetadata;

    /**
     * Create a new notification instance.
     */
    public function __construct(public int $remainingCodes)
    {
        $this->queue = 'emails';
    }

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
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => '2fa-recovery-code-used',
            'Remaining-Codes' => $this->remainingCodes,
            'Low-Codes-Alert' => $this->remainingCodes <= 3 ? 'yes' : 'no',
            'IP-Address' => request()->ip(),
            'Timestamp' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Código de Recuperación Utilizado - SAMI')
            ->greeting('¡Hola '.$notifiable->first_name.'!')
            ->line('Se ha utilizado un código de recuperación para acceder a tu cuenta.')
            ->line('Códigos restantes: '.$this->remainingCodes.'/10')
            ->line('Detalles del evento:')
            ->line('Fecha: '.now()->format('d/m/Y H:i:s'))
            ->line('IP: '.request()->ip());

        if ($this->remainingCodes <= 3) {
            $message->line('⚠️ ALERTA: Te quedan pocos códigos de recuperación.')
                ->line('Te recomendamos regenerar tus códigos de recuperación pronto.')
                ->action('Regenerar Códigos', url('/user/profile#two_factor_settings'));
        }

        $message->line('Si no reconoces este acceso, cambia tu contraseña inmediatamente.')
            ->salutation('Saludos, El equipo de SAMI')
            ->withSymfonyMessage(function ($msg) {
                $this->applyEmailMetadata($msg);
            });

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
            'used_at' => now(),
            'remaining_codes' => $this->remainingCodes,
            'ip_address' => request()->ip(),
        ];
    }
}
