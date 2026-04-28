<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorEnabledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Autenticación de Dos Factores Activada - SAMI')
            ->greeting('¡Hola '.$notifiable->first_name.'!')
            ->line('La autenticación de dos factores ha sido activada en tu cuenta.')
            ->line('Desde ahora, necesitarás tu aplicación de autenticación para iniciar sesión.')
            ->line('Detalles del evento:')
            ->line('Fecha: '.now()->format('d/m/Y H:i:s'))
            ->line('IP: '.request()->ip())
            ->line('Si no fuiste tú quien activó esta función, contacta inmediatamente al administrador.')
            ->action('Ir a Mi Perfil', url('/user/profile'))
            ->salutation('Saludos, El equipo de SAMI');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'enabled_at' => now(),
            'ip_address' => request()->ip(),
        ];
    }
}
