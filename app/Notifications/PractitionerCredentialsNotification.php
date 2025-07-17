<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return (new MailMessage)
            ->subject('Bienvenido al Sistema Meditech - Credenciales de Acceso')
            ->greeting('Estimado/a Dr. ' . $notifiable->first_name . ' ' . $notifiable->last_name)
            ->line('Le damos la bienvenida al sistema de gestión médica Meditech.')
            ->line('Sus credenciales de acceso han sido creadas exitosamente.')
            ->line('**Datos de acceso:**')
            ->line('Email: ' . $this->user->email)
            ->line('Contraseña temporal: ' . $this->temporaryPassword)
            ->line('**IMPORTANTE:** Esta es una contraseña temporal que debe cambiar en su primer acceso al sistema.')
            ->line('**Instrucciones de primer acceso:**')
            ->line('1. Ingrese al sistema con las credenciales proporcionadas')
            ->line('2. Será dirigido automáticamente a cambiar su contraseña')
            ->line('3. Cree una contraseña segura de su elección')
            ->line('4. Complete su perfil profesional')
            ->line('**Recomendaciones de seguridad:**')
            ->line('• Use una contraseña con al menos 8 caracteres')
            ->line('• Incluya letras mayúsculas, minúsculas y números')
            ->line('• No comparta sus credenciales con terceros')
            ->line('• Cierre sesión al terminar de usar el sistema')
            ->action('Acceder al Sistema', url('/login'))
            ->line('Si tiene alguna duda o problema para acceder, contacte al administrador del sistema.')
            ->salutation(env('APP_NAME'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'temporary_password' => $this->temporaryPassword,
            'message' => 'New practitioner credentials sent'
        ];
    }
}