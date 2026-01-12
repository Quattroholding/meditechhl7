<?php

namespace App\Notifications;

use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return array_filter([
            $this->getMailChannelIfValid($notifiable->email),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $expireTime = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
        $expireTimeInMinutes = $expireTime.' minutos';

        return (new MailMessage)
            ->subject('Restablecer Contraseña - '.config('app.name'))
            ->view('emails.reset-password', [
                'actionUrl' => $this->resetUrl($notifiable),
                'expireTime' => $expireTimeInMinutes,
                'user' => $notifiable,
            ]);
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl($notifiable)
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
