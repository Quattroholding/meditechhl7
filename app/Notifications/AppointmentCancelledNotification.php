<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancelledNotification extends Notification
{
    use Queueable;

    protected $appointment;
    protected $cancellationReason;
    protected $cancelledBy;

    public function __construct(Appointment $appointment, $cancellationReason = null, $cancelledBy = 'practitioner')
    {
        $this->appointment = $appointment;
        $this->cancellationReason = $cancellationReason;
        $this->cancelledBy = $cancelledBy;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $practitioner = $this->appointment->practitioner;
        $appointmentDate = $this->appointment->start_datetime;

        return (new MailMessage)
            ->subject('Cita Médica Cancelada')
            ->greeting('Estimado/a ' . $notifiable->name)
            ->line('Lamentamos informarle que su cita médica confirmada ha sido cancelada.')
            ->line('**Detalles de la cita cancelada:**')
            ->line('Médico: Dr. ' . $practitioner->name)
            ->line('Fecha: ' . $appointmentDate->format('d/m/Y'))
            ->line('Hora: ' . $appointmentDate->format('H:i'))
            ->line('Especialidad: ' . ($practitioner->specialty ?? 'Medicina General'))
            ->when($this->cancellationReason, function ($mail) {
                return $mail->line('**Motivo de cancelación:** ' . $this->cancellationReason);
            })
            ->line('**Próximos pasos:**')
            ->line('• Puede reagendar su cita contactándose con nuestro equipo')
            ->line('• Consulte nuevas fechas disponibles en el sistema')
            ->line('• Si es urgente, puede solicitar cita con otro especialista')
            ->action('Reagendar Cita', url('/appointments/reschedule'))
            ->line('Disculpe las molestias ocasionadas. Estamos aquí para ayudarle.')
            ->salutation('Atentamente,<br>Sistema de Gestión Médica');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'appointment_cancelled',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'appointment_datetime' => $this->appointment->start_datetime,
            'cancellation_reason' => $this->cancellationReason,
            'cancelled_by' => $this->cancelledBy,
            'message' => 'Su cita con Dr. ' . $this->appointment->practitioner->name . ' ha sido cancelada.'
        ];
    }
}
