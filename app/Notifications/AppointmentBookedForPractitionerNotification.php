<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\Appointment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class AppointmentBookedForPractitionerNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public Appointment $appointment
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        // Priorizar email si está disponible
        if ($this->isValidEmail($notifiable->email)) {
            $channels[] = 'mail';
        }
        // Si no tiene WhatsApp, usar email
        elseif ($notifiable->whatsapp_phone || $notifiable->phone) {
            $channels[] = WhatsAppMetaChannel::class;
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $patient = $this->appointment->patient;
        $appointmentDate = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        // Generate signed URLs that expire 1 day after the appointment
        // This gives practitioners time to confirm/cancel up until the appointment
        $expiresAt = $this->appointment->start->copy()->addDay();

        $confirmUrl = URL::temporarySignedRoute(
            'appointments.confirm',
            $expiresAt,
            ['appointment' => $this->appointment->id]
        );

        $cancelUrl = URL::temporarySignedRoute(
            'appointments.cancel',
            $expiresAt,
            ['appointment' => $this->appointment->id]
        );

        $mailMessage = (new MailMessage)
            ->subject('Nueva Cita Médica Agendada - '.$clinicName)
            ->view('emails.appointment-booked-practitioner', [
                'practitionerName' => $notifiable->name,
                'patientName' => $patient->name,
                'appointmentDate' => $appointmentDate->format('d/m/Y'),
                'appointmentTime' => $appointmentDate->format('H:i'),
                'durationMinutes' => $this->appointment->minutes_duration,
                'serviceType' => $this->appointment->service_type ?? 'Consulta',
                'specialty' => $this->appointment->medicalSpeciality->name ?? 'Medicina General',
                'branchName' => $this->appointment->consultingRoom->branch->name ?? 'N/A',
                'consultingRoom' => $this->appointment->consultingRoom->name ?? 'N/A',
                'clinicName' => $clinicName,
                'description' => $this->appointment->description,
                'comment' => $this->appointment->comment,
                'calendarUrl' => env('APP_SAMI_URL').'/appointments/calendar', // route('appointment.calendar')
                'confirmUrl' => $confirmUrl,
                'cancelUrl' => $cancelUrl,
            ]);

        // Agregar el usuario creador de la cita en BCC si es distinto del practitioner notificado
        $appointmentCreator = $this->appointment->getCreator();
        if ($appointmentCreator && $appointmentCreator->email !== $notifiable->email && $this->isValidEmail($appointmentCreator->email)) {
            $mailMessage->bcc($appointmentCreator->email);
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Standard notification fields
            'title' => 'Nueva Cita Médica Agendada',
            'message' => 'Nueva cita agendada con '.$this->appointment->patient->name,
            'steps' => array_filter([
                '👤 Paciente: '.$this->appointment->patient->name,
                '📅 Fecha: '.$this->appointment->start->format('d/m/Y'),
                '🕐 Hora: '.$this->appointment->start->format('H:i'),
                '⏱️ Duración: '.$this->appointment->minutes_duration.' minutos',
                $this->appointment->service_type ? '🩺 Tipo: '.$this->appointment->service_type : null,
                $this->appointment->consultingRoom->branch->name ? '🏪 Sede: '.$this->appointment->consultingRoom->branch->name : null,
                $this->appointment->consultingRoom->name ? '🚪 Consultorio: '.$this->appointment->consultingRoom->name : null,
                $this->appointment->comment ? '💬 Comentario: '.$this->appointment->comment : null,
            ]),
            'action' => [
                'text' => 'Ver en Calendario',
                'url' => route('appointment.calendar'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-calendar-check',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'appointment_booked_practitioner',
            'appointment_id' => $this->appointment->id,
            'patient_name' => $this->appointment->patient->name,
            'patient_id' => $this->appointment->patient->id,
            'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'appointment_date' => $this->appointment->start->format('Y-m-d'),
            'appointment_time' => $this->appointment->start->format('H:i'),
            'duration_minutes' => $this->appointment->minutes_duration,
            'service_type' => $this->appointment->service_type,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'description' => $this->appointment->description,
            'comment' => $this->appointment->comment,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $patient = $this->appointment->patient;
        $appointmentDate = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        $message = "✅ *Nueva Cita Médica Agendada*\n\n";
        $message .= "Hola Dr. {$notifiable->name},\n\n";
        $message .= "Se ha agendado una nueva cita médica:\n\n";

        $message .= "👤 *Paciente:* {$patient->name}\n";
        $message .= "📅 *Fecha:* {$appointmentDate->format('d/m/Y')}\n";
        $message .= "🕐 *Hora:* {$appointmentDate->format('H:i a')}\n";
        $message .= "⏱️ *Duración:* {$this->appointment->minutes_duration} minutos\n";

        if ($this->appointment->service_type) {
            $message .= "🩺 *Tipo de consulta:* {$this->appointment->service_type}\n";
        }

        if ($this->appointment->medicalSpeciality) {
            $message .= "🏥 *Especialidad:* {$this->appointment->medicalSpeciality->name}\n";
        }

        $message .= "🏢 *Clínica:* {$clinicName}\n";

        if ($this->appointment->consultingRoom->branch->name ?? null) {
            $message .= "🏪 *Sede:* {$this->appointment->consultingRoom->branch->name}\n";
        }

        if ($this->appointment->consultingRoom->name ?? null) {
            $message .= "🚪 *Consultorio:* {$this->appointment->consultingRoom->name}\n";
        }

        if ($this->appointment->comment) {
            $message .= "\n💬 *Comentario del paciente:*\n{$this->appointment->comment}\n";
        }

        $message .= "\n📱 Puede confirmar o gestionar la cita desde el sistema.\n";
        $message .= "\n¡Que tenga un excelente día!";

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
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', array_merge($context, [
                'practitioner_email' => $this->appointment->practitioner->email ?? 'N/A',
                'note' => 'El email del médico usa un dominio reservado (example.com, test.com, etc.)',
            ]));

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de cita agendada al médico', $context);
    }
}
