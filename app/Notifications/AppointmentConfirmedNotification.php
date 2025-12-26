<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public Appointment $appointment
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        $channels = ['mail', 'database'];

        // Add WhatsApp channel if user has WhatsApp phone number
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            $channels[] = \App\Channels\WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $practitioner = $this->appointment->practitioner;
        $confirmedDate = $this->appointment->start;
        $wasDateChanged = $this->appointment->wasDateTimeChanged();
        $clinicName = $this->appointment->client->name ?? config('app.name');

        $subject = $wasDateChanged
            ? 'Cita Reprogramada - Nueva Fecha Confirmada'
            : 'Cita Médica Confirmada';

        return (new MailMessage)
            ->subject($subject.' - '.$clinicName)
            ->view('emails.appointment-confirmed', [
                'patientName' => $notifiable->name,
                'practitionerName' => $practitioner->name,
                'appointmentDate' => $confirmedDate->format('d/m/Y'),
                'appointmentTime' => $confirmedDate->format('H:i'),
                'durationMinutes' => $this->appointment->minutes_duration,
                'serviceType' => $this->appointment->service_type ?? 'Consulta general',
                'clinicName' => $clinicName,
                'comment' => $this->appointment->comment,
                'patientInstruction' => $this->appointment->patient_instruction,
                'wasDateChanged' => $wasDateChanged,
                'originalDate' => $wasDateChanged ? $this->appointment->original_requested_datetime?->format('d/m/Y H:i') : null,
                'appointmentUrl' => route('appointment.calendar'),
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $wasDateChanged = $this->appointment->wasDateTimeChanged();

        return [
            // Standard notification fields
            'title' => $wasDateChanged ? 'Cita Reprogramada y Confirmada' : 'Cita Médica Confirmada',
            'message' => $wasDateChanged
                ? 'Su cita con Dr. '.$this->appointment->practitioner->name.' ha sido reprogramada y confirmada.'
                : 'Su cita con Dr. '.$this->appointment->practitioner->name.' ha sido confirmada.',
            'steps' => array_filter([
                '📅 Fecha: '.$this->appointment->start->format('d/m/Y'),
                '🕐 Hora: '.$this->appointment->start->format('H:i'),
                '⏱️ Duración: '.$this->appointment->minutes_duration.' minutos',
                $this->appointment->consultingRoom->branch->name ? '🏪 Sede: '.$this->appointment->consultingRoom->branch->name : null,
                $this->appointment->patient_instruction ? '📋 Instrucciones: '.$this->appointment->patient_instruction : null,
            ]),
            'action' => [
                'text' => 'Ver Calendario',
                'url' => route('appointment.calendar'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-calendar-check',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'appointment_confirmed',
            'appointment_id' => $this->appointment->id,
            'practitioner_name' => $this->appointment->practitioner->name,
            'practitioner_id' => $this->appointment->practitioner->id,
            'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'appointment_date' => $this->appointment->start->format('Y-m-d'),
            'appointment_time' => $this->appointment->start->format('H:i'),
            'duration_minutes' => $this->appointment->minutes_duration,
            'service_type' => $this->appointment->service_type,
            'was_rescheduled' => $wasDateChanged,
            'original_requested_datetime' => $wasDateChanged ? $this->appointment->original_requested_datetime?->format('Y-m-d H:i:s') : null,
            'clinic_name' => $this->appointment->client->name ?? null,
            'branch_name' => $this->appointment->consultingRoom->branch->name ?? null,
            'consulting_room' => $this->appointment->consultingRoom->name ?? null,
            'comment' => $this->appointment->comment,
            'patient_instruction' => $this->appointment->patient_instruction,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $practitioner = $this->appointment->practitioner;
        $confirmedDate = $this->appointment->start;
        $wasDateChanged = $this->appointment->wasDateTimeChanged();
        $clinicName = $this->appointment->client->name ?? config('app.name');

        if ($wasDateChanged) {
            $message = "✅ *Cita Reprogramada y Confirmada*\n\n";
            $message .= "Hola {$notifiable->name},\n\n";
            $message .= "Su cita ha sido reprogramada y confirmada:\n\n";
        } else {
            $message = "✅ *Cita Médica Confirmada*\n\n";
            $message .= "Hola {$notifiable->name},\n\n";
            $message .= "Su cita médica ha sido confirmada:\n\n";
        }

        $message .= "👨‍⚕️ *Doctor:* {$practitioner->name}\n";
        $message .= "📅 *Fecha:* {$confirmedDate->format('d/m/Y')}\n";
        $message .= "🕐 *Hora:* {$confirmedDate->format('H:i a')}\n";
        $message .= "⏱️ *Duración:* {$this->appointment->minutes_duration} minutos\n";
        $message .= "🏢 *Clínica:* {$clinicName}\n";

        if ($this->appointment->consultingRoom->branch->name ?? null) {
            $message .= "🏪 *Sede:* {$this->appointment->consultingRoom->branch->name}\n";
        }

        if ($this->appointment->consultingRoom->name ?? null) {
            $message .= "🚪 *Consultorio:* {$this->appointment->consultingRoom->name}\n";
        }

        if ($wasDateChanged && $this->appointment->original_requested_datetime) {
            $message .= "\n⏰ *Fecha original:* {$this->appointment->original_requested_datetime->format('d/m/Y H:i')}\n";
        }

        if ($this->appointment->patient_instruction) {
            $message .= "\n📋 *Instrucciones:*\n{$this->appointment->patient_instruction}\n";
        }

        $message .= "\nPor favor llegue 15 minutos antes de su cita.\n";
        $message .= '¡Esperamos verle pronto! 😊';

        return $message;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Falló el envío de notificación de cita confirmada', [
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'practitioner_id' => $this->appointment->practitioner_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
