<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 600];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Appointment $appointment
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Verify appointment still exists and is in booked status
        if (!$this->appointment->exists || $this->appointment->status !== 'booked') {
            Log::info('Appointment reminder job cancelled - appointment no longer exists or not booked', [
                'appointment_id' => $this->appointment->id,
                'status' => $this->appointment->status ?? 'deleted'
            ]);
            return;
        }

        // Verify appointment is still in the future
        if ($this->appointment->start->isPast()) {
            Log::info('Appointment reminder job cancelled - appointment is in the past', [
                'appointment_id' => $this->appointment->id,
                'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s')
            ]);
            return;
        }

        // Get the patient
        $patient = $this->appointment->patient;
        
        if (!$patient || !$patient->email) {
            Log::warning('Appointment reminder job cancelled - patient not found or no email', [
                'appointment_id' => $this->appointment->id,
                'patient_id' => $this->appointment->patient_id
            ]);
            return;
        }

        try {
            // Send the reminder notification
            $patient->notify(new AppointmentReminderNotification($this->appointment));

            Log::info('Appointment reminder sent successfully', [
                'appointment_id' => $this->appointment->id,
                'patient_id' => $patient->id,
                'patient_email' => $patient->email,
                'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send appointment reminder', [
                'appointment_id' => $this->appointment->id,
                'patient_id' => $patient->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e; // Re-throw to trigger job retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Appointment reminder job failed permanently', [
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'appointment_datetime' => $this->appointment->start->format('Y-m-d H:i:s'),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}