<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\Appointment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRescheduledNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public Appointment $appointment,
        public Carbon $originalDateTime,
        public ?string $reason = null
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        // Priorizar WhatsApp si está disponible
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            $channels[] = WhatsAppMetaChannel::class;
        }
        // Si no tiene WhatsApp, usar email
        elseif ($this->isValidEmail($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => 'appointment-rescheduled',
            'Appointment-ID' => $this->appointment->id,
            'Patient-ID' => $this->appointment->patient_id,
            'Patient-Name' => $this->appointment->patient->full_name ?? 'N/A',
            'Doctor-ID' => $this->appointment->practitioner_id,
            'Doctor-Name' => $this->appointment->practitioner->name ?? 'N/A',
            'Original-Date' => $this->originalDateTime->format('Y-m-d H:i'),
            'New-Date' => $this->appointment->start->format('Y-m-d H:i'),
            'Branch-Name' => $this->appointment->consultingRoom->branch->name ?? 'N/A',
            'Reschedule-Reason' => $this->reason ?? 'N/A',
            'Client-ID' => $this->appointment->client_id,
        ];
    }

    public function toMail($notifiable)
    {
        $practitioner = $this->appointment->practitioner;
        $newDateTime = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        return (new MailMessage)
            ->subject('Cambio de Horario en su Cita Médica - '.$clinicName)
            // ->bcc('rgasperi@smartcarebilling.com')
            ->view('emails.appointment-rescheduled', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'originalDate' => $this->originalDateTime->format('d/m/Y'),
                'originalTime' => $this->originalDateTime->format('H:i'),
                'newDate' => $newDateTime->format('d/m/Y'),
                'newTime' => $newDateTime->format('H:i'),
                'durationMinutes' => $this->appointment->minutes_duration,
                'specialty' => $this->appointment->medicalSpeciality->name ?? 'Medicina General',
                'clinicName' => $clinicName,
                'branchName' => $this->appointment->consultingRoom->branch->name ?? null,
                'consultingRoom' => $this->appointment->consultingRoom->name ?? null,
                'reason' => $this->reason,
                'appointmentUrl' => route('appointment.calendar'),
            ])
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    public function toArray($notifiable)
    {
        return [
            // Standard notification fields
            'title' => 'Cambio de Horario en su Cita',
            'message' => 'Su cita con '.$this->appointment->practitioner->name.' ha sido reprogramada.',
            'steps' => array_filter([
                '❌ Fecha anterior: '.$this->originalDateTime->format('d/m/Y H:i'),
                '✅ Nueva fecha: '.$this->appointment->start->format('d/m/Y H:i'),
                '⏱️ Duración: '.$this->appointment->minutes_duration.' minutos',
                $this->appointment->consultingRoom->branch->name ? '🏪 Sede: '.$this->appointment->consultingRoom->branch->name : null,
                $this->appointment->consultingRoom->name ? '🚪 Consultorio: '.$this->appointment->consultingRoom->name : null,
                $this->reason ? '📝 Motivo: '.$this->reason : null,
            ]),
            'action' => [
                'text' => 'Ver Calendario',
                'url' => route('appointment.calendar'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-calendar-alt',

            // Legacy/specific fields
            'type' => 'appointment_rescheduled',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'practitioner_id' => $this->appointment->practitioner->id,
            'original_datetime' => $this->originalDateTime->format('Y-m-d H:i:s'),
            'original_date' => $this->originalDateTime->format('Y-m-d'),
            'original_time' => $this->originalDateTime->format('H:i'),
            'new_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'new_date' => $this->appointment->start->format('Y-m-d'),
            'new_time' => $this->appointment->start->format('H:i'),
            'duration_minutes' => $this->appointment->minutes_duration,
            'service_type' => $this->appointment->service_type,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'reason' => $this->reason,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $practitioner = $this->appointment->practitioner;
        $newDateTime = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        $message = "🔄 *Cambio de Horario en su Cita*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Le informamos que su cita médica ha sido *reprogramada*:\n\n";

        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";

        if ($this->appointment->medicalSpeciality) {
            $message .= "🏥 *Especialidad:* {$this->appointment->medicalSpeciality->name}\n";
        }

        $message .= "\n❌ *Horario anterior:*\n";
        $message .= "📅 Fecha: {$this->originalDateTime->format('d/m/Y')}\n";
        $message .= "🕐 Hora: {$this->originalDateTime->format('H:i a')}\n";

        $message .= "\n✅ *Nuevo horario:*\n";
        $message .= "📅 Fecha: {$newDateTime->format('d/m/Y')}\n";
        $message .= "🕐 Hora: {$newDateTime->format('H:i a')}\n";
        $message .= "⏱️ Duración: {$this->appointment->minutes_duration} minutos\n";

        $message .= "\n🏢 *Clínica:* {$clinicName}\n";

        if ($this->appointment->consultingRoom->branch->name ?? null) {
            $message .= "🏪 *Sede:* {$this->appointment->consultingRoom->branch->name}\n";
        }

        if ($this->appointment->consultingRoom->name ?? null) {
            $message .= "🚪 *Consultorio:* {$this->appointment->consultingRoom->name}\n";
        }

        if ($this->reason) {
            $message .= "\n📝 *Motivo del cambio:*\n{$this->reason}\n";
        }

        $message .= "\n⏰ *Recordatorio:* Recibirá una notificación 2 horas antes de su nueva cita.\n";
        $message .= "\nPor favor llegue 15 minutos antes de su cita.\n";
        $message .= "\nDisculpe las molestias.\n";
        $message .= '¡Esperamos verle pronto! 😊';

        return $message;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $context = [
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'practitioner_id' => $this->appointment->practitioner_id,
            'original_datetime' => $this->originalDateTime->format('Y-m-d H:i:s'),
            'new_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'reason' => $this->reason,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', array_merge($context, [
                'patient_email' => $this->appointment->patient->email ?? 'N/A',
                'note' => 'El email del paciente usa un dominio reservado (example.com, test.com, etc.)',
            ]));

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de cita reprogramada', $context);
    }
}
