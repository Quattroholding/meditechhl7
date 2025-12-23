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

    protected $first_time_login;

    public function __construct(User $user, string $temporaryPassword, $first_time_login = true)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
        $this->first_time_login = $first_time_login;
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
                'first_time_login' => $this->first_time_login,
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
