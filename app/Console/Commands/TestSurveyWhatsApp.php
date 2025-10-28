<?php

namespace App\Console\Commands;

use App\Jobs\SendSurveyWhatsAppJob;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestSurveyWhatsApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'survey:test-whatsapp
                            {--patient= : Patient ID to test with}
                            {--encounter= : Encounter ID to test with}
                            {--phone= : Override phone number for testing}
                            {--test-api : Test API endpoints only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp survey system end-to-end';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing WhatsApp Survey System');
        $this->newLine();

        // Check configuration
        if (! $this->checkConfiguration()) {
            return 1;
        }

        // Test API endpoints if flag is set
        if ($this->option('test-api')) {
            return $this->testApiEndpoints();
        }

        // Get or create test data
        $patient = $this->getTestPatient();
        $encounter = $this->getTestEncounter($patient);
        $survey = $this->getActiveSurvey();

        if (! $patient || ! $encounter || ! $survey) {
            $this->error('❌ Could not get test data');

            return 1;
        }

        // Create survey response
        $this->info('📝 Creating survey response...');
        $surveyResponse = $this->createSurveyResponse($survey, $patient, $encounter);

        if (! $surveyResponse) {
            $this->error('❌ Failed to create survey response');

            return 1;
        }

        $this->info("✅ Survey response created: ID {$surveyResponse->id}, Token: {$surveyResponse->token}");
        $this->newLine();

        // Always test webhook directly (skip job queue)
        return $this->testWebhookDirectly($surveyResponse, $encounter);
    }

    /**
     * Check system configuration
     */
    private function checkConfiguration(): bool
    {
        $this->info('🔧 Checking configuration...');

        $checks = [
            'N8N Webhook URL' => config('services.n8n.survey_webhook_url'),
            'WhatsApp Enabled' => config('services.n8n.survey_whatsapp_enabled'),
            'Testing Mode' => config('services.n8n.testing_mode'),
            'Testing Phone' => config('services.n8n.testing_phone'),
        ];

        $allGood = true;
        foreach ($checks as $name => $value) {
            $status = $value ? '✅' : '❌';
            $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : ($value ?: 'not set');
            $this->line("{$status} {$name}: {$displayValue}");

            if (! $value && $name !== 'Testing Phone') {
                $allGood = false;
            }
        }

        $this->newLine();

        if (! $allGood) {
            $this->error('❌ Configuration incomplete. Please check your .env file:');
            $this->line('N8N_SURVEY_WEBHOOK_URL=https://your-n8n.com/webhook/survey-start');
            $this->line('N8N_SURVEY_WHATSAPP_ENABLED=true');

            return false;
        }

        return true;
    }

    /**
     * Get test patient
     */
    private function getTestPatient(): ?Patient
    {
        if ($this->option('patient')) {
            $patient = Patient::find($this->option('patient'));
            if ($patient) {
                $this->info("📱 Using patient: {$patient->given_name} {$patient->family_name} (ID: {$patient->id})");

                return $patient;
            }
        }

        // Get first patient with phone
        $patient = Patient::whereNotNull('phone')->first();
        if ($patient) {
            $this->info("📱 Using patient: {$patient->given_name} {$patient->family_name} (ID: {$patient->id})");

            return $patient;
        }

        $this->error('❌ No patient found with phone number');

        return null;
    }

    /**
     * Get test encounter
     */
    private function getTestEncounter(?Patient $patient): ?Encounter
    {
        if ($this->option('encounter')) {
            $encounter = Encounter::find($this->option('encounter'));
            if ($encounter) {
                $this->info("🏥 Using encounter: ID {$encounter->id}");

                return $encounter;
            }
        }

        // Get first encounter for patient
        if ($patient) {
            $encounter = Encounter::where('patient_id', $patient->id)
                ->whereHas('practitioner')
                ->first();

            if ($encounter) {
                $this->info("🏥 Using encounter: ID {$encounter->id}");

                return $encounter;
            }
        }

        // Get any encounter
        $encounter = Encounter::whereHas('practitioner')
            ->whereHas('patient')
            ->first();

        if ($encounter) {
            $this->info("🏥 Using encounter: ID {$encounter->id}");

            return $encounter;
        }

        $this->error('❌ No encounter found');

        return null;
    }

    /**
     * Get active survey
     */
    private function getActiveSurvey(): ?Survey
    {
        $survey = Survey::where('status', 'active')
            ->where('trigger_point', 'after_encounter')
            ->where('is_active', true)
            ->with('questions')
            ->first();

        if ($survey) {
            $questionCount = $survey->questions->count();
            $this->info("📋 Using survey: {$survey->title} ({$questionCount} questions)");

            return $survey;
        }

        $this->error('❌ No active survey found with trigger_point=after_encounter');
        $this->line('💡 Run: php artisan db:seed --class=PatientSatisfactionSurveySeeder');

        return null;
    }

    /**
     * Create survey response
     */
    private function createSurveyResponse(Survey $survey, Patient $patient, Encounter $encounter): ?SurveyResponse
    {
        try {
            return SurveyResponse::create([
                'survey_id' => $survey->id,
                'patient_id' => $patient->id,
                'encounter_id' => $encounter->id,
                'practitioner_id' => $encounter->practitioner_id,
                'client_id' => $encounter->appointment->client_id ?? 1,
                'medical_speciality_id' => $encounter->appointment->medical_speciality_id ?? null,
                'status' => 'pending',
            ]);
        } catch (\Exception $e) {
            $this->error("Error creating survey response: {$e->getMessage()}");

            return null;
        }
    }

    /**
     * Test via job dispatch
     */
    private function testViaJob(SurveyResponse $surveyResponse, Encounter $encounter): int
    {
        $this->info('🚀 Dispatching SendSurveyWhatsAppJob...');

        try {
            SendSurveyWhatsAppJob::dispatch($surveyResponse, $encounter);
            $this->info('✅ Job dispatched successfully!');
            $this->newLine();
            $this->line('💡 Check queue worker logs to see job execution:');
            $this->line('   tail -f storage/logs/laravel.log | grep -i survey');
            $this->newLine();
            $this->line('💡 Make sure queue worker is running:');
            $this->line('   php artisan queue:work --queue=whatsapp');

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to dispatch job: {$e->getMessage()}");

            return 1;
        }
    }

    /**
     * Test webhook directly
     */
    private function testWebhookDirectly(SurveyResponse $surveyResponse, Encounter $encounter): int
    {
        $this->info('🌐 Testing webhook directly...');

        $patient = $surveyResponse->patient;
        $survey = $surveyResponse->survey;

        $patientPhone = $this->option('phone')
            ?? config('services.n8n.testing_phone')
            ?? $patient->phone;

        $webhookData = [
            'survey_response_id' => $surveyResponse->id,
            'survey_response_token' => $surveyResponse->token,
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->given_name.' '.$patient->family_name,
                'phone' => $patientPhone,
            ],
            'practitioner' => [
                'id' => $encounter->practitioner_id,
                'name' => $encounter->practitioner?->name,
            ],
            'survey' => [
                'id' => $survey->id,
                'title' => $survey->title,
                'description' => $survey->description,
                'total_questions' => $survey->questions()->count(),
            ],
            'encounter' => [
                'id' => $encounter->id,
                'date' => $encounter->start->format('Y-m-d H:i:s'),
            ],

            'api_endpoints' => [
                'next_question' => url("/api/surveys/{$surveyResponse->token}/next-question"),
                'save_response' => url('/api/surveys/save-response'),
                'progress' => url("/api/surveys/{$surveyResponse->token}/progress"),
            ],
        ];

        $this->info('📤 Sending webhook to: '.config('services.n8n.survey_webhook_url'));
        $this->newLine();

        try {
            $response = Http::timeout(30)
                ->post(config('services.n8n.survey_webhook_url'), $webhookData);

            if ($response->successful()) {
                $this->info('✅ Webhook sent successfully!');
                $this->line('Response: '.$response->body());
            } else {
                $this->error("❌ Webhook failed with status {$response->status()}");
                $this->line('Response: '.$response->body());

                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Failed to send webhook: {$e->getMessage()}");

            return 1;
        }

        $this->newLine();
        $this->testApiEndpointsForSurvey($surveyResponse);

        return 0;
    }

    /**
     * Test API endpoints
     */
    private function testApiEndpoints(): int
    {
        $this->info('🧪 Testing API endpoints...');
        $this->newLine();

        // Find a survey response to test with
        $surveyResponse = SurveyResponse::where('status', 'pending')->first();

        if (! $surveyResponse) {
            $this->warn('No pending survey response found. Creating one...');

            $patient = Patient::first();
            $encounter = Encounter::first();
            $survey = Survey::where('is_active', true)->first();

            if (! $patient || ! $encounter || ! $survey) {
                $this->error('❌ Could not create test survey response');

                return 1;
            }

            $surveyResponse = $this->createSurveyResponse($survey, $patient, $encounter);
        }

        return $this->testApiEndpointsForSurvey($surveyResponse);
    }

    /**
     * Test API endpoints for a survey response
     */
    private function testApiEndpointsForSurvey(SurveyResponse $surveyResponse): int
    {
        $baseUrl = url('/');
        $token = $surveyResponse->token;

        // Test 1: Get next question
        $this->info('1️⃣  Testing: GET /api/surveys/{token}/next-question');
        try {
            $response = Http::get("{$baseUrl}/api/surveys/{$token}/next-question");

            if ($response->successful()) {
                $data = $response->json();
                $this->info('   ✅ Success!');
                if (isset($data['question'])) {
                    $this->line("   📝 Question: {$data['question']['question_text']}");
                    $this->line("   📊 Progress: {$data['progress']['current_question']}/{$data['progress']['total_questions']}");
                }
            } else {
                $this->error("   ❌ Failed: {$response->status()}");

                return 1;
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Exception: {$e->getMessage()}");

            return 1;
        }

        $this->newLine();

        // Test 2: Get progress
        $this->info('2️⃣  Testing: GET /api/surveys/{token}/progress');
        try {
            $response = Http::get("{$baseUrl}/api/surveys/{$token}/progress");

            if ($response->successful()) {
                $data = $response->json();
                $this->info('   ✅ Success!');
                $this->line("   📊 Progress: {$data['progress']['answered_questions']}/{$data['progress']['total_questions']} ({$data['progress']['percentage']}%)");
            } else {
                $this->error("   ❌ Failed: {$response->status()}");

                return 1;
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Exception: {$e->getMessage()}");

            return 1;
        }

        $this->newLine();
        $this->info('✅ All API endpoint tests passed!');
        $this->newLine();
        $this->line("💡 Survey Response Token: {$token}");
        $this->line('💡 You can use this token to test manually:');
        $this->line("   curl {$baseUrl}/api/surveys/{$token}/next-question");

        return 0;
    }
}
