<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\Appointment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRescheduledForPractitionerNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public Appointment $appointment,
        public Carbon $originalDateTime,
        public ?string $reason = null,
        public ?string $changedBy = null
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        // Only add mail channel if email is valid and not reserved
        if ($this->isValidEmail($notifiable->email)) {
            $channels[] = 'mail';
        }

        // Add WhatsApp channel if user has WhatsApp phone number
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            $channels[] = WhatsAppMetaChannel::class;
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $patient = $this->appointment->patient;
        $newDate = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        $mailMessage = (new MailMessage)
            ->subject('Cita Reprogramada - '.$clinicName)
            ->view('emails.appointment-rescheduled-practitioner', [
                'practitionerName' => $notifiable->name,
                'patientName' => $patient->name,
                'originalDate' => $this->originalDateTime->format('d/m/Y'),
                'originalTime' => $this->originalDateTime->format('H:i'),
                'newDate' => $newDate->format('d/m/Y'),
                'newTime' => $newDate->format('H:i'),
                'durationMinutes' => $this->appointment->minutes_duration,
                'serviceType' => $this->appointment->service_type ?? 'Consulta',
                'specialty' => $this->appointment->medicalSpeciality->name ?? 'Medicina General',
                'branchName' => $this->appointment->consultingRoom->branch->name ?? 'N/A',
                'consultingRoom' => $this->appointment->consultingRoom->name ?? 'N/A',
                'reason' => $this->reason,
                'changedBy' => $this->changedBy,
                'clinicName' => $clinicName,
                'appointmentUrl' => route('appointment.calendar'),
            ]);

        // Agregar el usuario creador de la cita en BCC si es distinto del practitioner notificado
        $appointmentCreator = $this->appointment->getCreator();
        if ($appointmentCreator && $appointmentCreator->email !== $notifiable->email && $this->isValidEmail($appointmentCreator->email)) {
            $mailMessage->bcc($appointmentCreator->email);
        }

        return $mailMessage;
    }

    public function toArray(object $notifiable): array
    {
        return [
            // Standard notification fields
            'title' => 'Cita Reprogramada',
            'message' => 'Se ha reprogramado una cita con el paciente '.$this->appointment->patient->name,
            'steps' => array_filter([
                '👤 Paciente: '.$this->appointment->patient->name,
                '📅 Fecha Original: '.$this->originalDateTime->format('d/m/Y H:i'),
                '📅 Nueva Fecha: '.$this->appointment->start->format('d/m/Y H:i'),
                '⏱️ Duración: '.$this->appointment->minutes_duration.' minutos',
                $this->appointment->medicalSpeciality ? '🎯 Especialidad: '.$this->appointment->medicalSpeciality->name : null,
                $this->reason ? '📋 Motivo: '.$this->reason : null,
                $this->changedBy ? '👤 Modificado por: '.$this->changedBy : null,
            ]),
            'action' => [
                'text' => 'Ver Calendario',
                'url' => route('appointment.calendar'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-calendar-alt',

            // Legacy/specific fields
            'type' => 'appointment_rescheduled_practitioner',
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'patient_name' => $this->appointment->patient->name,
            'original_datetime' => $this->originalDateTime->format('Y-m-d H:i:s'),
            'new_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'duration_minutes' => $this->appointment->minutes_duration,
            'service_type' => $this->appointment->service_type,
            'specialty' => $this->appointment->medicalSpeciality->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'reason' => $this->reason,
            'changed_by' => $this->changedBy,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $patient = $this->appointment->patient;
        $newDate = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        $message = "🔄 *Cita Reprogramada*\n\n";
        $message .= "Hola Dr. {$notifiable->name},\n\n";
        $message .= "Se ha reprogramado una cita:\n\n";

        $message .= "👤 *Paciente:* {$patient->name}\n";
        $message .= "📅 *Fecha Original:* {$this->originalDateTime->format('d/m/Y')} a las {$this->originalDateTime->format('H:i')}\n";
        $message .= "📅 *Nueva Fecha:* {$newDate->format('d/m/Y')} a las {$newDate->format('H:i')}\n";
        $message .= "⏱️ *Duración:* {$this->appointment->minutes_duration} minutos\n";
        $message .= "🏢 *Clínica:* {$clinicName}\n";

        if ($this->appointment->consultingRoom->branch->name ?? null) {
            $message .= "🏪 *Sede:* {$this->appointment->consultingRoom->branch->name}\n";
        }

        if ($this->appointment->consultingRoom->name ?? null) {
            $message .= "🚪 *Consultorio:* {$this->appointment->consultingRoom->name}\n";
        }

        if ($this->reason) {
            $message .= "\n📋 *Motivo del cambio:*\n{$this->reason}\n";
        }

        if ($this->changedBy) {
            $message .= "\n👤 *Modificado por:* {$this->changedBy}\n";
        }

        return $message;
    }
}
