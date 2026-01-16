<?php

namespace App\Notifications;

use App\Models\Encounter;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Services\EncounterPrescriptionPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class EncounterPrescriptionNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    /**
     * IDs of medication requests to include in PDF
     */
    public array $medicationRequestIds = [];

    /**
     * IDs of service requests to include in PDF
     */
    public array $serviceRequestIds = [];

    public function __construct(
        public Encounter $encounter,
        public bool $hasMedications = false,
        public bool $hasServiceRequests = false
    ) {
        $this->onQueue('emails');

        // Store the IDs of items to send (before they get marked as sent)
        if ($hasMedications) {
            $this->medicationRequestIds = $encounter->medicationRequests()
                ->whereNull('notification_sent_at')
                ->pluck('id')
                ->toArray();
        }

        if ($hasServiceRequests) {
            $this->serviceRequestIds = $encounter->serviceRequests()
                ->whereNull('notification_sent_at')
                ->pluck('id')
                ->toArray();
        }
    }

    public function via($notifiable)
    {
        return array_filter([
            'database',
            $this->getMailChannelIfValid($notifiable->email),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $practitioner = $this->encounter->practitioner;
        $patient = $this->encounter->patient;

        $subject = 'Sus Recetas Medicas - Dr(a). '.($practitioner->name ?? 'Medico').' - SAMI';

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->view('emails.encounter-prescription', [
                'encounter' => $this->encounter,
                'practitioner' => $practitioner,
                'patient' => $patient,
                'hasMedications' => $this->hasMedications,
                'hasServiceRequests' => $this->hasServiceRequests,
            ]);

        // Use unified PDF service
        $pdfService = new EncounterPrescriptionPdfService;

        // Generate and attach prescription PDF (medications)
        if ($this->hasMedications && ! empty($this->medicationRequestIds)) {
            $prescriptionPdf = $pdfService->generatePrescriptionPdf($this->encounter, $this->medicationRequestIds);
            if ($prescriptionPdf) {
                $filename = $pdfService->generatePrescriptionFilename($this->encounter);
                $mailMessage->attachData($prescriptionPdf, $filename, [
                    'mime' => 'application/pdf',
                ]);
            }
        }

        // Generate and attach medical order PDF (service requests)
        if ($this->hasServiceRequests && ! empty($this->serviceRequestIds)) {
            $medicalOrderPdf = $pdfService->generateMedicalOrderPdf($this->encounter, $this->serviceRequestIds);
            if ($medicalOrderPdf) {
                $filename = $pdfService->generateMedicalOrderFilename($this->encounter);
                $mailMessage->attachData($medicalOrderPdf, $filename, [
                    'mime' => 'application/pdf',
                ]);
            }
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
        $practitioner = $this->encounter->practitioner;
        $attachments = [];

        if ($this->hasMedications) {
            $attachments[] = 'Receta de medicamentos';
        }
        if ($this->hasServiceRequests) {
            $attachments[] = 'Ordenes medicas';
        }

        return [
            // Standard notification fields
            'title' => 'Nuevas Recetas Medicas',
            'message' => 'Ha recibido nuevas recetas medicas del Dr(a). '.($practitioner->name ?? 'Medico').'. Los documentos han sido enviados a su correo electronico.',
            'steps' => $attachments,
            'action' => [
                'text' => 'Iniciar Sesion',
                'url' => route('login'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-file-medical',

            // Legacy/specific fields
            'type' => 'encounter_prescription',
            'encounter_id' => $this->encounter->id,
            'practitioner_id' => $this->encounter->practitioner_id,
            'practitioner_name' => $practitioner->name ?? null,
            'patient_id' => $this->encounter->patient_id,
            'has_medications' => $this->hasMedications,
            'has_service_requests' => $this->hasServiceRequests,
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $context = [
            'encounter_id' => $this->encounter->id,
            'patient_id' => $this->encounter->patient_id,
            'practitioner_id' => $this->encounter->practitioner_id,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            Log::warning('Intento de envio de receta a direccion reservada RFC 2606', $context);

            return;
        }

        // Log other errors as errors
        Log::error('Fallo el envio de notificacion de recetas medicas', $context);
    }
}
