<?php

namespace App\Jobs;

use App\Models\Encounter;
use App\Models\SurveyResponse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSurveyWhatsAppJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SurveyResponse $surveyResponse,
        public Encounter $encounter
    ) {
        $this->onQueue('whatsapp');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $patient = $this->surveyResponse->patient;
            $survey = $this->surveyResponse->survey;

            // Verify patient has WhatsApp phone
            if (! $patient->phone) {
                Log::warning('No se puede enviar encuesta por WhatsApp: paciente sin teléfono', [
                    'patient_id' => $patient->id,
                    'survey_response_id' => $this->surveyResponse->id,
                ]);

                return;
            }

            // Get n8n webhook URL from config
            $n8nWebhookUrl = config('services.n8n.survey_webhook_url');

            if (! $n8nWebhookUrl) {
                Log::error('n8n webhook URL no configurada para encuestas');

                return;
            }

            $patient_phone = $patient->phone;

            if (config('app.env') === 'local' || config('services.n8n.testing_mode')) {
                $patient_phone = config('services.n8n.testing_phone');
            }

            // Prepare data for n8n
            $webhookData = [
                'survey_response_id' => $this->surveyResponse->id,
                'survey_response_token' => $this->surveyResponse->token,
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->given_name.' '.$patient->family_name,
                    'phone' => $patient_phone,
                ],
                'practitioner' => [
                    'id' => $this->encounter->practitioner_id,
                    'name' => $this->encounter->practitioner?->name,
                ],
                'survey' => [
                    'id' => $survey->id,
                    'title' => $survey->title,
                    'description' => $survey->description,
                    'total_questions' => $survey->questions()->count(),
                ],
                'encounter' => [
                    'id' => $this->encounter->id,
                    'date' => $this->encounter->created_at->format('Y-m-d H:i:s'),
                ],
                'api_endpoints' => [
                    'next_question' => url("/api/surveys/{$this->surveyResponse->token}/next-question"),
                    'save_response' => url('/api/surveys/save-response'),
                    'progress' => url("/api/surveys/{$this->surveyResponse->token}/progress"),
                ],
            ];

            // Send webhook to n8n
            $response = Http::timeout(30)
                ->post($n8nWebhookUrl, $webhookData);

            if ($response->successful()) {
                Log::info('Encuesta de WhatsApp enviada exitosamente a n8n', [
                    'patient_id' => $patient->id,
                    'patient_phone' => $patient->phone,
                    'survey_id' => $survey->id,
                    'survey_response_id' => $this->surveyResponse->id,
                    'encounter_id' => $this->encounter->id,
                    'n8n_response' => $response->json(),
                ]);
            } else {
                Log::error('Error en respuesta de n8n', [
                    'survey_response_id' => $this->surveyResponse->id,
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                ]);

                throw new \Exception("n8n webhook failed with status {$response->status()}");
            }

        } catch (\Exception $e) {
            Log::error('Error al enviar encuesta de WhatsApp a n8n', [
                'survey_response_id' => $this->surveyResponse->id,
                'encounter_id' => $this->encounter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Falló el envío de encuesta por WhatsApp después de todos los intentos', [
            'survey_response_id' => $this->surveyResponse->id,
            'encounter_id' => $this->encounter->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
