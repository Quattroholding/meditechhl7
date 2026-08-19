<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PractitionerCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    public $tries = 3;

    public $backoff = [60, 300, 600];

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
        return array_filter([
            $this->getMailChannelIfValid($notifiable->email),
        ]);
    }

    /**
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => $this->first_time_login ? 'practitioner-credentials-welcome' : 'practitioner-credentials-update',
            'User-ID' => $this->user->id,
            'User-Name' => $this->user->full_name,
            'User-Email' => $this->user->email,
            'User-Role' => $this->user->hasRole('doctor') ? 'doctor' : 'staff',
            'First-Time-Login' => $this->first_time_login ? 'yes' : 'no',
        ];
    }

    public function toMail($notifiable)
    {
        $userPrefix = '';
        if ($this->user->hasRole('doctor')) {
            $userPrefix = 'Dr(a). ';
        }

        return (new MailMessage)
            ->subject('Bienvenido a SAMI - Credenciales de Acceso')
            // ->bcc('business@meditecpty.com')
            ->view('emails.practitioner-credentials', [
                'user' => $this->user,
                'temporaryPassword' => $this->temporaryPassword,
                'userPrefix' => $userPrefix,
                'first_time_login' => $this->first_time_login,
            ])
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    public function toArray(object $notifiable): array
    {
        $userPrefix = $this->user->hasRole('doctor') ? 'Dr(a). ' : '';

        return [
            // Standard notification fields
            'title' => $this->first_time_login ? 'Bienvenido a SAMI' : 'Credenciales de Acceso',
            'message' => $this->first_time_login
                ? 'Bienvenido '.$userPrefix.$this->user->full_name.'. Sus credenciales de acceso han sido enviadas a su correo electrónico.'
                : 'Sus credenciales de acceso han sido actualizadas.',
            'steps' => [
                '📧 Email: '.$this->user->email,
                '🔑 Contraseña temporal enviada por correo',
                '⚠️ Deberá cambiar su contraseña en el primer inicio de sesión',
            ],
            'action' => [
                'text' => 'Iniciar Sesión',
                'url' => route('login'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-user-md',

            // Legacy/specific fields (for backwards compatibility)
            'user_id' => $this->user->id,
            'temporary_password' => $this->temporaryPassword,
        ];
    }
}
