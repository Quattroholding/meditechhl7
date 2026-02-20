<?php

namespace App\Console\Commands;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Notifications\SendPatientSatisfactionSurvey;
use Illuminate\Console\Command;

class TestSatisfactionSurveyTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'survey:test-meta-template
                            {--patient= : Patient ID to test with}
                            {--encounter= : Encounter ID to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test satisfaction survey WhatsApp Meta template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing WhatsApp Meta Template for Satisfaction Survey');
        $this->newLine();

        // Check Meta configuration
        if (! $this->checkConfiguration()) {
            return 1;
        }

        // Get test data
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

        $this->info("✅ Survey response created: ID {$surveyResponse->id}");
        $this->info('📋 Survey URL: '.route('survey.public', $surveyResponse->token));
        $this->newLine();

        // Send notification
        $this->info('📤 Sending WhatsApp notification using Meta template...');

        try {
            // Display what will be sent
            $this->displayTemplatePreview($surveyResponse, $encounter, $patient);

            if (! $this->confirm('Do you want to send this notification?', true)) {
                $this->warn('⚠️  Notification cancelled by user');

                return 0;
            }

            $patient->notify(new SendPatientSatisfactionSurvey(
                $surveyResponse,
                $encounter,
                $survey
            ));

            $this->info('✅ Notification sent successfully!');
            $this->newLine();
            $this->line('💡 Check logs for delivery status:');
            $this->line('   tail -f storage/logs/laravel.log | grep -i whatsapp');

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Failed to send notification: {$e->getMessage()}");
            $this->error("Trace: {$e->getTraceAsString()}");

            return 1;
        }
    }

    /**
     * Check Meta configuration
     */
    private function checkConfiguration(): bool
    {
        $this->info('🔧 Checking Meta WhatsApp configuration...');

        $checks = [
            'Phone Number ID' => config('services.meta.whatsapp_phone_number_id'),
            'Access Token' => config('services.meta.whatsapp_access_token') ? '***' : null,
            'Testing Mode' => config('services.meta.testing_mode'),
            'Testing Phone' => config('services.meta.testing_phone'),
        ];

        $allGood = true;
        foreach ($checks as $name => $value) {
            $status = $value ? '✅' : '❌';
            $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : ($value ?: 'not set');
            $this->line("{$status} {$name}: {$displayValue}");

            if (! $value && $name !== 'Testing Phone' && $name !== 'Testing Mode') {
                $allGood = false;
            }
        }

        $this->newLine();

        if (! $allGood) {
            $this->error('❌ Configuration incomplete. Please check your .env file:');
            $this->line('META_WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id');
            $this->line('META_WHATSAPP_ACCESS_TOKEN=your_access_token');

            return false;
        }

        return true;
    }

    /**
     * Display template preview
     */
    private function displayTemplatePreview(SurveyResponse $surveyResponse, Encounter $encounter, Patient $patient): void
    {
        $surveyUrl = route('survey.public', $surveyResponse->token);
        $encounterDate = $encounter->start->format('d/m/Y');

        // Get clinic/branch name
        $locationName = $encounter->appointment->consultingRoom->branch->name
            ?? $encounter->appointment->client->name
            ?? config('app.name');

        $this->newLine();
        $this->info('📋 Template Preview:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('Template Name: satisfaction_survey');
        $this->line('Language: es (Spanish)');
        $this->newLine();
        $this->line('Header:');
        $this->line('  ¿Qué te pareció nuestro servicio?');
        $this->newLine();
        $this->line('Body:');
        $this->line("  Gracias por visitarnos en {$locationName} el {$encounterDate}.");
        $this->line('');
        $this->line('  Valoramos tus comentarios.');
        $this->line('');
        $this->line('  Completa esta breve encuesta para que sepamos cómo');
        $this->line('  podemos seguir mejorando.');
        $this->newLine();
        $this->line('Button:');
        $this->line('  [Completar encuesta] → '.$surveyUrl);
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->line('Variables:');
        $this->line("  {{1}} (dirección): {$locationName}");
        $this->line("  {{2}} (fecha): {$encounterDate}");
        $this->newLine();
        $this->line('To (Phone): '.($patient->whatsapp_phone ?: $patient->phone));
        if (config('services.meta.testing_mode')) {
            $this->warn('⚠️  Testing mode is enabled. Will send to: '.config('services.meta.testing_phone'));
        }
        $this->newLine();
    }

    /**
     * Get test patient
     */
    private function getTestPatient(): ?Patient
    {
        if ($this->option('patient')) {
            $patient = Patient::find($this->option('patient'));
            if ($patient) {
                $this->info("📱 Using patient: {$patient->name} (ID: {$patient->id})");

                return $patient;
            }
        }

        // Get first patient with phone
        $patient = Patient::whereNotNull('phone')->first();
        if ($patient) {
            $this->info("📱 Using patient: {$patient->name} (ID: {$patient->id})");

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
}
