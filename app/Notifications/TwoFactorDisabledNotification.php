<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorDisabledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ?string $disabledBy = null,
        public ?string $reason = null
    ) {
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('⚠️ Autenticación de Dos Factores Desactivada - SAMI')
            ->greeting('¡Hola '.$notifiable->first_name.'!')
            ->line('La autenticación de dos factores ha sido desactivada en tu cuenta.');

        if ($this->disabledBy) {
            $message->line('Esta acción fue realizada por: '.$this->disabledBy);
        }

        if ($this->reason) {
            $message->line('Razón: '.$this->reason);
        }

        $message->line('Detalles del evento:')
            ->line('Fecha: '.now()->format('d/m/Y H:i:s'))
            ->line('IP: '.request()->ip())
            ->line('⚠️ IMPORTANTE: Tu cuenta ahora está menos protegida. Te recomendamos reactivar 2FA lo antes posible.')
            ->action('Reactivar 2FA', url('/user/profile#two_factor_settings'))
            ->line('Si no reconoces esta acción, contacta inmediatamente al administrador.')
            ->salutation('Saludos, El equipo de SAMI');

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
            'disabled_at' => now(),
            'disabled_by' => $this->disabledBy,
            'reason' => $this->reason,
            'ip_address' => request()->ip(),
        ];
    }
}
