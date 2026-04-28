<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorBackupCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $code)
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
        $expiry = config('two-factor.email_backup.expiry', 15);

        return (new MailMessage)
            ->subject('Código de Respaldo 2FA - SAMI')
            ->greeting('¡Hola '.$notifiable->first_name.'!')
            ->line('Has solicitado un código de respaldo para acceder a tu cuenta.')
            ->line('Tu código temporal es:')
            ->line('**'.$this->code.'**')
            ->line("Este código expirará en {$expiry} minutos.")
            ->line('Si no solicitaste este código, ignora este mensaje.')
            ->line('IP: '.request()->ip())
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
            'code' => $this->code,
            'expires_at' => now()->addMinutes(config('two-factor.email_backup.expiry', 15)),
        ];
    }
}
