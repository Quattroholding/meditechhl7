<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PractitionerCredentialsNotification extends Notification
{
    use Queueable;

    protected $user;

    protected $temporaryPassword;

    public function __construct(User $user, string $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $userPrefix = '';
        if ($this->user->hasRole('doctor')) {
            $userPrefix = 'Dr(a). ';
        }

        return (new MailMessage)
            ->subject('Bienvenido a SAMI - Credenciales de Acceso')
            ->view('emails.practitioner-credentials', [
                'user' => $this->user,
                'temporaryPassword' => $this->temporaryPassword,
                'userPrefix' => $userPrefix,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'temporary_password' => $this->temporaryPassword,
            'message' => 'New practitioner credentials sent',
        ];
    }
}
