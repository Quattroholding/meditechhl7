<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmedNotification extends Notification
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
        $practitioner = $this->appointment->practitioner;
        $confirmedDate = $this->appointment->start;
        $wasDateChanged = $this->appointment->wasDateTimeChanged();

        $subject = $wasDateChanged
            ? 'Cita Reprogramada - Nueva Fecha Confirmada'
            : 'Cita Médica Confirmada';

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Estimado/a ' . $notifiable->name);

        if ($wasDateChanged) {
            $originalDate = $this->appointment->original_requested_datetime;
            $mail->line('Su cita médica ha sido reprogramada por el médico.')
                ->line('**Fecha original solicitada:** ' . $originalDate->format('d/m/Y H:i'))
                ->line('**Nueva fecha confirmada:** ' . $confirmedDate->format('d/m/Y H:i'));
        } else {
            $mail->line('Su cita médica ha sido confirmada para la fecha y hora solicitada.');
        }

        return $mail
            ->line('**Detalles de su cita:**')
            ->line('Médico: Dr. ' . $practitioner->name)
            ->line('Fecha: ' . $confirmedDate->format('d/m/Y'))
            ->line('Hora: ' . $confirmedDate->format('H:i'))
            ->line('Duración estimada: ' . $this->appointment->minutes_duration . ' minutos')
            ->line('Tipo de consulta: ' . ($this->appointment->service_type ?? 'Consulta general'))
            ->when($this->appointment->comment, function ($mail) {
                return $mail->line('Nota del médico: ' . $this->appointment->comment);
            })
            ->line('**Instrucciones importantes:**')
            ->line('• Llegue 15 minutos antes de su cita')
            ->line('• Traiga su documento de identidad')
            ->line('• Traiga sus exámenes médicos previos si los tiene')
            ->when($this->appointment->patient_instruction, function ($mail) {
                return $mail->line('• ' . $this->appointment->patient_instruction);
            })
            ->action('Ver Detalles de la Cita', url('/appointments/' . $this->appointment->id))
            ->line('Si necesita reprogramar o cancelar su cita, por favor contáctenos con al menos 24 horas de anticipación.')
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
