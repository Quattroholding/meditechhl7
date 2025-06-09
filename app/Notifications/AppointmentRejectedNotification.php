<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRejectedNotification extends Notification
{
    use Queueable;

    protected $appointment;
    protected $rejectionReason;

    public function __construct(Appointment $appointment, $rejectionReason = null)
    {
        $this->appointment = $appointment;
        $this->rejectionReason = $rejectionReason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // También guardamos en BD para historial
    }

    public function toMail($notifiable)
    {
        $practitioner = $this->appointment->practitioner;
        $requestedDate = $this->appointment->original_requested_datetime;

        return (new MailMessage)
            ->subject('Cita Médica No Disponible')
            ->greeting('Estimado/a ' . $notifiable->name)
            ->line('Lamentamos informarle que su solicitud de cita médica no ha podido ser confirmada.')
            ->line('**Detalles de la solicitud:**')
            ->line('Médico solicitado: ' . $practitioner->name)
            ->line('Fecha solicitada: ' . $requestedDate->format('d/m/Y'))
            ->line('Hora solicitada: ' . $requestedDate->format('H:i'))
            ->line('Especialidad: ' . ($practitioner->specialty ?? 'Medicina General'))
            ->when($this->rejectionReason, function ($mail) {
                return $mail->line('**Motivo:** ' . $this->rejectionReason);
            })
            ->line('**¿Qué puede hacer ahora?**')
            ->line('• Puede solicitar una nueva cita en fechas y horarios diferentes')
            ->line('• Consulte la disponibilidad del médico en nuestro sistema')
            ->line('• Considere solicitar cita con otro especialista disponible')
            ->action('Ver Médicos Disponibles', url('/doctors/available'))
            ->line('Nuestro equipo está disponible para ayudarle a encontrar una nueva fecha conveniente.')
            ->line('Puede contactarnos directamente para asistencia personalizada.')
            ->salutation('Gracias por su comprensión,<br>Sistema de Gestión Médica');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'appointment_rejected',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'requested_datetime' => $this->appointment->original_requested_datetime,
            'rejection_reason' => $this->rejectionReason,
            'message' => 'Su solicitud de cita con Dr. ' . $this->appointment->practitioner->name . ' ha sido rechazada.'
        ];
    }
}
