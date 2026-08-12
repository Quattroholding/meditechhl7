<?php

namespace App\Notifications;

use App\Channels\WhatsAppMetaChannel;
use App\Models\Encounter;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendPatientSatisfactionSurvey extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SurveyResponse $surveyResponse,
        public Encounter $encounter,
        public Survey $survey
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
            // Use WhatsApp Meta channel with template
            $channels[] = WhatsAppMetaChannel::class;
        }

        return $channels;
    }

    /**
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => 'patient-satisfaction-survey',
            'Survey-ID' => $this->survey->id,
            'Survey-Response-ID' => $this->surveyResponse->id,
            'Encounter-ID' => $this->encounter->id,
            'Patient-ID' => $this->surveyResponse->patient_id,
            'Patient-Name' => $this->surveyResponse->patient->full_name ?? 'N/A',
            'Doctor-ID' => $this->encounter->practitioner_id,
            'Doctor-Name' => $this->encounter->appointment->practitioner->name ?? 'N/A',
            'Encounter-Date' => $this->encounter->start->format('Y-m-d'),
            'Survey-Token' => $this->surveyResponse->token,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $surveyUrl = route('survey.public', $this->surveyResponse->token);
        $practitionerName = $this->encounter->appointment->practitioner->name ?? 'Su médico';
        $encounterDate = $this->encounter->start->format('d/m/Y');
        $clinicName = $this->encounter->appointment->client->name;

        return (new MailMessage)
            ->subject('Encuesta de Satisfacción - '.$clinicName)
            ->view('emails.patient-satisfaction-survey', [
                'patientName' => $notifiable->name,
                'surveyTitle' => $this->survey->title,
                'surveyUrl' => $surveyUrl,
                'practitionerName' => $practitionerName,
                'encounterDate' => $encounterDate,
                'clinicName' => $clinicName,
            ])
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $practitionerName = $this->encounter->appointment->practitioner->name ?? 'su médico';

        return [
            // Standard notification fields
            'title' => 'Encuesta de Satisfacción',
            'message' => 'Ayúdenos a mejorar completando nuestra encuesta de satisfacción sobre su consulta reciente.',
            'steps' => [
                '📅 Consulta del: '.$this->encounter->start->format('d/m/Y'),
                '👨‍⚕️ Con: '.$practitionerName,
                '⏱️ Solo tomará unos minutos',
            ],
            'action' => [
                'text' => 'Completar Encuesta',
                'url' => route('survey.public', $this->surveyResponse->token),
            ],
            'priority' => 'normal',
            'icon' => 'fas fa-clipboard-list',

            // Legacy/specific fields (for backwards compatibility)
            'survey_id' => $this->survey->id,
            'survey_title' => $this->survey->title,
            'survey_response_id' => $this->surveyResponse->id,
            'encounter_id' => $this->encounter->id,
            'encounter_date' => $this->encounter->start->format('Y-m-d'),
            'practitioner_name' => $practitionerName,
            'survey_url' => route('survey.public', $this->surveyResponse->token),
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * Returns an array structure to use WhatsApp Business template
     * Uses Meta's satisfaction_survey template in Spanish
     */
    public function toWhatsApp(object $notifiable): array
    {
        // Get only the token, not the full URL
        // The template in Meta already has the base URL configured
        $surveyToken = $this->surveyResponse->token;

        // Get clinic/branch name or use client name
        $locationName = $this->encounter->appointment->consultingRoom->branch->name
            ?? $this->encounter->appointment->client->name
            ?? config('app.name');

        // Format date in Spanish format
        $encounterDate = $this->encounter->start->format('d/m/Y');

        // Build template components for satisfaction_survey
        $components = [];

        // Body component with 2 variables:
        // {{1}} = Location/clinic name
        // {{2}} = Date
        $components[] = [
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $locationName],
                ['type' => 'text', 'text' => $encounterDate],
            ],
        ];

        // Button component with only the dynamic part (token)
        // The base URL is already configured in Meta template
        $components[] = [
            'type' => 'button',
            'sub_type' => 'url',
            'index' => '0',
            'parameters' => [
                ['type' => 'text', 'text' => $surveyToken],
            ],
        ];

        return [
            'use_template' => true,
            'template_name' => 'satisfaction_survey',
            'language_code' => 'es',
            'components' => $components,
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $context = [
            'survey_response_id' => $this->surveyResponse->id,
            'encounter_id' => $this->encounter->id,
            'patient_id' => $this->surveyResponse->patient_id,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', array_merge($context, [
                'patient_email' => $this->surveyResponse->patient->email ?? 'N/A',
                'note' => 'El email del paciente usa un dominio reservado (example.com, test.com, etc.)',
            ]));

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de encuesta de satisfacción', $context);
    }
}
