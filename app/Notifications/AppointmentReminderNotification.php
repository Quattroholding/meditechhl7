<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public Appointment $appointment
    ) {
        $this->onQueue('emails');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Only add mail channel if email is valid and not reserved
        if ($this->isValidEmail($notifiable->email)) {
            $channels[] = 'mail';
        }

        // Add WhatsApp channel if user has WhatsApp phone number
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            // Use N8N channel instead of Twilio
            $channels[] = \App\Channels\WhatsAppN8NChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $practitioner = $this->appointment->practitioner;
        $appointmentDate = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');

        return (new MailMessage)
            ->subject('Recordatorio de Cita Médica - '.$clinicName)
            ->view('emails.appointment-reminder', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'appointmentDate' => $appointmentDate->format('d/m/Y'),
                'appointmentTime' => $appointmentDate->format('H:i a'),
                'durationMinutes' => $this->appointment->minutes_duration,
                'serviceType' => $this->appointment->service_type ?? 'Consulta general',
                'specialty' => $this->appointment->medicalSpeciality->name ?? 'Medicina General',
                'clinicName' => $clinicName,
                'branchName' => $this->appointment->consultingRoom->branch->name ?? 'N/A',
                'consultingRoom' => $this->appointment->consultingRoom->name ?? 'N/A',
                'comment' => $this->appointment->comment,
                'patientInstruction' => $this->appointment->patient_instruction,
                'appointmentUrl' => route('appointment.calendar'),
                'hoursUntilAppointment' => now()->diffInHours($appointmentDate),
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $appointmentDate = $this->appointment->start;
        $hoursUntil = now()->diffInHours($appointmentDate);

        return [
            // Standard notification fields
            'title' => 'Recordatorio de Cita Médica',
            'message' => "Su cita con Dr. {$this->appointment->practitioner->name} es en {$hoursUntil} horas",
            'steps' => array_filter([
                '📅 Fecha: '.$appointmentDate->format('d/m/Y'),
                '🕐 Hora: '.$appointmentDate->format('H:i'),
                '⏱️ Duración: '.$this->appointment->minutes_duration.' minutos',
                $this->appointment->consultingRoom->branch->name ? '🏪 Sede: '.$this->appointment->consultingRoom->branch->name : null,
                $this->appointment->consultingRoom->name ? '🚪 Consultorio: '.$this->appointment->consultingRoom->name : null,
                $this->appointment->patient_instruction ? '📋 Instrucciones: '.$this->appointment->patient_instruction : null,
            ]),
            'action' => [
                'text' => 'Ver Cita',
                'url' => route('appointment.calendar'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-bell',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'appointment_reminder',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'practitioner_id' => $this->appointment->practitioner->id,
            'appointment_datetime' => $appointmentDate->format('Y-m-d H:i:s'),
            'appointment_date' => $appointmentDate->format('Y-m-d'),
            'appointment_time' => $appointmentDate->format('H:i'),
            'duration_minutes' => $this->appointment->minutes_duration,
            'service_type' => $this->appointment->service_type,
            'specialty' => $this->appointment->practitioner->specialty,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'comment' => $this->appointment->comment,
            'patient_instruction' => $this->appointment->patient_instruction,
            'hours_until_appointment' => $hoursUntil,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $practitioner = $this->appointment->practitioner;
        $appointmentDate = $this->appointment->start;
        $clinicName = $this->appointment->client->name ?? config('app.name');
        $hoursUntil = Carbon::parse($appointmentDate)->diffForHumans();

        $message = "🏥 *Recordatorio de Cita Médica*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Le recordamos su cita médica:\n\n";
        $message .= "📋 *Cita #{$this->appointment->id}*\n";
        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";
        $message .= "📅 *Fecha:* {$appointmentDate->format('d/m/Y')}\n";
        $message .= "🕐 *Hora:* {$appointmentDate->format('H:i a')}\n";
        $message .= "⏱️ *Duración:* {$this->appointment->minutes_duration} minutos\n";
        $message .= "🏢 *Clínica:* {$clinicName}\n";

        if ($this->appointment->consultingRoom->branch->name ?? null) {
            $message .= "🏪 *Sede:* {$this->appointment->consultingRoom->branch->name}\n";
        }

        if ($this->appointment->consultingRoom->name ?? null) {
            $message .= "🚪 *Consultorio:* {$this->appointment->consultingRoom->name}\n";
        }

        if ($this->appointment->medicalSpeciality->name ?? null) {
            $message .= "🔬 *Especialidad:* {$this->appointment->medicalSpeciality->name}\n";
        }

        $message .= "\n⏰ *Su cita es en 2 horas*\n\n";

        if ($this->appointment->patient_instruction) {
            $message .= "📋 *Instrucciones:*\n{$this->appointment->patient_instruction}\n\n";
        }

        $message .= "Por favor llegue 15 minutos antes de su cita.\n\n";

        // Add interactive action instructions
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "*ACCIONES RÁPIDAS:*\n\n";
        $message .= "Para confirmar o cancelar su cita, responda a este mensaje:\n\n";
        $message .= "✅ Para confirmar: CONFIRMAR {$this->appointment->id}\n";
        $message .= "❌ Para cancelar: CANCELAR {$this->appointment->id}\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";

        $message .= "Si necesita reprogramar, contáctenos con anticipación.\n\n";
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
            'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
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
        \Log::error('Falló el envío de recordatorio de cita', $context);
    }
}
