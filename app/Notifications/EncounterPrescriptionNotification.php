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
        $channels = ['database'];

        // Try WhatsApp first
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            $channels[] = \App\Channels\WhatsAppMetaChannel::class;
        }

        // Add email as fallback
        if ($this->getMailChannelIfValid($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $practitioner = $this->encounter->practitioner;
        $patient = $this->encounter->patient;

        $subject = 'Sus Recetas Medicas - '.($practitioner->name ?? 'Medico').' - SAMI';

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
            'message' => 'Ha recibido nuevas recetas medicas del '.($practitioner->name ?? 'Medico').'. Los documentos han sido enviados a su correo electrónico.',
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
     * Get the WhatsApp representation of the notification.
     * Returns array with documents to send
     */
    public function toWhatsApp(object $notifiable): ?array
    {
        try {
            $practitioner = $this->encounter->practitioner;
            $patient = $this->encounter->patient;
            $pdfService = new EncounterPrescriptionPdfService;
            $whatsAppService = app(\App\Services\WhatsAppService::class);

            $documents = [];

            // Generate and store prescription PDF (medications)
            if ($this->hasMedications && ! empty($this->medicationRequestIds)) {
                $prescriptionPdf = $pdfService->generatePrescriptionPdf($this->encounter, $this->medicationRequestIds);
                if ($prescriptionPdf) {
                    $filename = $pdfService->generatePrescriptionFilename($this->encounter);
                    $url = $whatsAppService->storePdfTemporarily($prescriptionPdf, $filename);

                    if ($url) {
                        $documents[] = [
                            'url' => $url,
                            'filename' => $filename,
                            'caption' => 'Receta Medica - '.($practitioner->name ?? 'Medico'),
                        ];
                    }
                }
            }

            // Generate and store medical order PDF (service requests)
            if ($this->hasServiceRequests && ! empty($this->serviceRequestIds)) {
                $medicalOrderPdf = $pdfService->generateMedicalOrderPdf($this->encounter, $this->serviceRequestIds);
                if ($medicalOrderPdf) {
                    $filename = $pdfService->generateMedicalOrderFilename($this->encounter);
                    $url = $whatsAppService->storePdfTemporarily($medicalOrderPdf, $filename);

                    if ($url) {
                        $documents[] = [
                            'url' => $url,
                            'filename' => $filename,
                            'caption' => 'Orden Medica - '.($practitioner->name ?? 'Medico'),
                        ];
                    }
                }
            }

            if (empty($documents)) {
                Log::warning('No se pudieron generar documentos para WhatsApp', [
                    'encounter_id' => $this->encounter->id,
                    'patient_id' => $patient->id,
                ]);

                return null;
            }

            // Build message
            $message = "📄 *Nuevas Recetas Medicas*\n\n";
            $message .= "Hola {$patient->name},\n\n";
            $message .= "Ha recibido nuevas recetas medicas del *{$practitioner->name}*.\n\n";

            if ($this->hasMedications) {
                $message .= "💊 Receta de medicamentos\n";
            }
            if ($this->hasServiceRequests) {
                $message .= "🏥 Ordenes medicas\n";
            }

            $message .= "\nLos documentos se enviaran a continuacion.";

            return [
                'body' => $message,
                'documents' => $documents,
            ];

        } catch (\Exception $e) {
            Log::error('Error al generar mensaje de WhatsApp para recetas', [
                'encounter_id' => $this->encounter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
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
