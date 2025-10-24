<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Patient;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Console\Command;

class TestWhatsAppNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test {--phone} {--patient-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp notification functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->option('phone');
        $patientId = $this->option('patient-id');



        try {
            // Find or create a test patient
            if ($patientId) {
                $patient = Patient::find($patientId);
                if (! $patient) {
                    $this->error("Patient with ID {$patientId} not found.");

                    return 1;
                }
            } else {
                $patient = Patient::first();
                if (! $patient) {
                    $this->error('No patients found in database.');

                    return 1;
                }
            }
            if(empty($phone)) $phone = $patient->phone;
                // Update patient's WhatsApp phone
            $patient->update(['whatsapp_phone' => $phone]);
            $this->info("Updated patient {$patient->name} WhatsApp phone to: {$phone}");

            // Find an appointment for testing
            $appointment = Appointment::where('patient_id', $patient->id)->first();
            if (! $appointment) {
                $this->error("No appointments found for patient {$patient->name}.");

                return 1;
            }

            // Send test notification synchronously (not queued)
            $this->info('Sending WhatsApp test notification...');
            $this->newLine();

            // Create notification but don't queue it
            $notification = new AppointmentReminderNotification($appointment);

            // Get channels
            $channels = $notification->via($patient);
            $this->line('Channels: '.implode(', ', array_map(function ($channel) {
                return is_string($channel) ? class_basename($channel) : $channel;
            }, $channels)));
            $this->newLine();

            // Send via WhatsApp channel directly
            if (in_array(\App\Channels\WhatsAppN8NChannel::class, $channels)) {
                $whatsappChannel = app(\App\Channels\WhatsAppN8NChannel::class);
                $whatsappChannel->send($patient, $notification);

                $this->info('✅ WhatsApp notification sent successfully!');
            } else {
                $this->warn('⚠️  WhatsApp N8N channel not in notification channels');
            }

            $this->newLine();
            $this->info("Patient: {$patient->name}");
            $this->info("Phone: {$phone}");
            $this->info("Appointment: {$appointment->start->format('d/m/Y H:i')} with Dr. {$appointment->practitioner->name}");

        } catch (\Exception $e) {
            $this->error('❌ Failed to send WhatsApp notification: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
