<?php

namespace App\Console\Commands;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Notifications\SendPatientSatisfactionSurvey;
use Illuminate\Console\Command;

class TestSurveyNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'survey:test-notification
                            {--patient= : Patient ID to test with}
                            {--encounter= : Encounter ID to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test survey notification via WhatsApp (simple link version)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Survey Notification with Link');
        $this->newLine();

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
        $surveyResponse = SurveyResponse::create([
            'survey_id' => $survey->id,
            'patient_id' => $patient->id,
            'encounter_id' => $encounter->id,
            'practitioner_id' => $encounter->practitioner_id,
            'client_id' => $encounter->appointment->client_id ?? 1,
            'medical_speciality_id' => $encounter->appointment->medical_speciality_id ?? null,
            'status' => 'pending',
        ]);

        $this->info("✅ Survey response created: ID {$surveyResponse->id}, Token: {$surveyResponse->token}");
        $this->newLine();

        // Generate survey URL
        $surveyUrl = route('survey.public', $surveyResponse->token);
        $this->info("🔗 Survey URL: {$surveyUrl}");
        $this->newLine();

        // Send notification
        $this->info('📤 Sending notification...');

        try {
            $patient->notify(new SendPatientSatisfactionSurvey($surveyResponse, $encounter, $survey));

            $this->info('✅ Notification sent successfully!');
            $this->newLine();

            $this->line('📊 Notification will be sent via:');
            if ($patient->email) {
                $this->line("   ✉️  Email: {$patient->email}");
            }
            if ($patient->phone || $patient->whatsapp_phone) {
                $phone = $patient->whatsapp_phone ?? $patient->phone;
                $this->line("   📱 WhatsApp: {$phone}");
            }
            $this->line('   🗄️  Database notification');

            $this->newLine();
            $this->line('💡 Check queue logs to see the notification being processed:');
            $this->line('   php artisan queue:work');
            $this->newLine();
            $this->line('💡 Or check logs:');
            $this->line('   tail -f storage/logs/laravel.log | grep -i survey');

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send notification: {$e->getMessage()}");

            return 1;
        }
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
}
