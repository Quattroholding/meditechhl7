<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        $channels = ['mail', 'database'];
        
        // Add WhatsApp channel if user has WhatsApp phone number
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            $channels[] = \App\Channels\WhatsAppChannel::class;
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
            ->subject('Recordatorio de Cita Médica - ' . $clinicName)
            ->view('emails.appointment-reminder', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'appointmentDate' => $appointmentDate->format('d/m/Y'),
                'appointmentTime' => $appointmentDate->format('H:i a'),
                'durationMinutes' => $this->appointment->minutes_duration,
                'serviceType' => $this->appointment->service_type ?? 'Consulta general',
                'specialty' =>  $this->appointment->medicalSpeciality->name ?? 'Medicina General',
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
            'message' => "Recordatorio: Su cita con Dr. {$this->appointment->practitioner->name} es en {$hoursUntil} horas",
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
        $hoursUntil = now()->diffInHours($appointmentDate);

        $message = "🏥 *Recordatorio de Cita Médica*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "Le recordamos su cita médica:\n\n";
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

        $message .= "\n⏰ *Su cita es en {$hoursUntil} horas*\n\n";

        if ($this->appointment->patient_instruction) {
            $message .= "📋 *Instrucciones:*\n{$this->appointment->patient_instruction}\n\n";
        }

        $message .= "Por favor llegue 15 minutos antes de su cita.\n";
        $message .= "Si necesita reprogramar, contáctenos con anticipación.\n\n";
        $message .= "¡Esperamos verle pronto! 😊";

        return $message;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Falló el envío de recordatorio de cita', [
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'practitioner_id' => $this->appointment->practitioner_id,
            'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'error' => $exception->getMessage()
        ]);
    }
}
