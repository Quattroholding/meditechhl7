<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentProposedNotification extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $patient = $this->appointment->patient;
        $requestedDate = $this->appointment->original_requested_datetime;

        return (new MailMessage)
            ->subject('Nueva Solicitud de Cita Médica')
            ->greeting('Estimado ' . $notifiable->name)
            ->line('Ha recibido una nueva solicitud de cita médica.')
            ->line('**Detalles de la cita:**')
            ->line('Paciente: ' . $patient->name)
            ->line('Fecha solicitada: ' . $requestedDate->format('d/m/Y'))
            ->line('Hora solicitada: ' . $requestedDate->format('H:i'))
            ->line('Sucursal: ' . $this->appointment->consultingRoom->branch->name)
            ->line('Consultorio: ' . $this->appointment->consultingRoom->name)
            ->line('Duración: ' . $this->appointment->minutes_duration . ' minutos')
            ->line('Tipo de servicio: ' . ($this->appointment->service_type ?? 'Consulta'))
            ->when($this->appointment->description, function ($mail) {
                return $mail->line('Descripción: ' . $this->appointment->description);
            })
            ->when($this->appointment->comment, function ($mail) {
                return $mail->line('Comentario del paciente: ' . $this->appointment->comment);
            })
            ->line('Por favor, confirme o reprograme esta cita según su disponibilidad.')
            ->action('Revisar Cita', url('/appointments/pending/' . $this->appointment->id))
            ->line('Gracias por su atención.')
            ->salutation(env('APP_NAME'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
