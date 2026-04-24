<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $registeredUser
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return array_filter([
            'database',
            $this->getMailChannelIfValid($notifiable->email),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $practitioner = $this->registeredUser->practitioner;

        return (new MailMessage)
            ->subject('Nuevo Registro Pendiente de Validación - SAMI Recetas')
            ->greeting('Hola '.$notifiable->first_name.',')
            ->line('Se ha registrado un nuevo médico en la aplicación móvil SAMI Recetas.')
            ->line('Los datos del registro son:')
            ->line('**Nombre:** '.$this->registeredUser->first_name.' '.$this->registeredUser->last_name)
            ->line('**Email:** '.$this->registeredUser->email)
            ->line('**Teléfono:** '.($practitioner->phone ?? 'No especificado'))
            ->line('**Registro Médico:** '.($practitioner->registry ?? 'No especificado'))
            ->line('**Fecha de Registro:** '.$this->registeredUser->created_at->format('d/m/Y H:i'))
            ->line('Este usuario requiere validación de documentos antes de poder acceder al sistema.')
            ->action('Revisar Documentos', route('user.pending-validations'))
            ->line('Por favor, revisa los documentos subidos (cédula e idoneidad médica) y aprueba o rechaza el registro.')
            ->salutation('Saludos, Sistema SAMI Recetas');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->registeredUser->id,
            'user_name' => $this->registeredUser->first_name.' '.$this->registeredUser->last_name,
            'user_email' => $this->registeredUser->email,
            'registry' => $this->registeredUser->practitioner?->registry,
            'registered_at' => $this->registeredUser->created_at->toDateTimeString(),
            'message' => 'Nuevo registro pendiente de validación: '.$this->registeredUser->first_name.' '.$this->registeredUser->last_name,
        ];
    }
}
