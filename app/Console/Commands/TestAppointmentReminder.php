<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Practitioner;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestAppointmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointment:test-reminder {--patient_id=} {--practitioner_id=} {--hours=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test appointment reminder notification scheduling';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing appointment reminder notification...');
        $this->newLine();

        // Get patient and practitioner
        $patientId = $this->option('patient_id');
        $practitionerId = $this->option('practitioner_id');
        $hoursInFuture = (int) $this->option('hours');

        if (! $patientId) {
            $patient = Patient::whereNotNull('email')->first();
            if (! $patient) {
                $this->error('No patients found with email addresses.');

                return 1;
            }
        } else {
            $patient = Patient::find($patientId);
            if (! $patient) {
                $this->error("Patient with ID {$patientId} not found.");

                return 1;
            }
        }

        if (! $practitionerId) {
            $practitioner = Practitioner::first();
            if (! $practitioner) {
                $this->error('No practitioners found.');

                return 1;
            }
        } else {
            $practitioner = Practitioner::find($practitionerId);
            if (! $practitioner) {
                $this->error("Practitioner with ID {$practitionerId} not found.");

                return 1;
            }
        }

        // Create test appointment
        $start = Carbon::now()->addHours($hoursInFuture);
        $end = $start->copy()->addMinutes(30);

        $this->info('Creating test appointment for:');
        $this->info("  Patient: {$patient->name} ({$patient->email})");
        $this->info("  Practitioner: {$practitioner->name}");
        $this->info("  Start: {$start->format('Y-m-d H:i:s')}");
        $this->info("  End: {$end->format('Y-m-d H:i:s')}");
        $this->newLine();

        $appointment = Appointment::create([
            'fhir_id' => 'appointment-'.Str::uuid(),
            'identifier' => 'TEST-'.strtoupper(Str::random(7)),
            'patient_id' => $patient->id,
            'practitioner_id' => $practitioner->id,
            'client_id' => $practitioner->user->default_client_id ?? 1,
            'start' => $start,
            'end' => $end,
            'minutes_duration' => 30,
            'status' => 'booked',
            'service_type' => 'Consulta de prueba',
            'description' => 'Cita de prueba para testing de notificaciones de recordatorio',
        ]);

        $this->info("Appointment created with ID: {$appointment->id}");
        $this->newLine();

        // Schedule reminder
        $this->info('Scheduling reminder notification...');
        $result = $appointment->notifyPatientAboutAppointment();

        if ($result) {
            $reminderTime = $start->copy()->subHours(2);
            $this->info('✓ Reminder notification scheduled successfully!');
            $this->info("  Reminder will be sent at: {$reminderTime->format('Y-m-d H:i:s')}");
            $this->info("  Time until reminder: {$reminderTime->diffForHumans()}");
        } else {
            $this->warn('⚠ Reminder was not scheduled (appointment is less than 2 hours away)');
        }

        $this->newLine();
        $this->info('Test completed. Check the logs at storage/logs/laravel.log for details.');
        $this->info("To delete test appointment, run: php artisan tinker then Appointment::find({$appointment->id})->delete()");

        return 0;
    }
}
