<?php

namespace App\Jobs;

use App\Mail\MedicalLeaveMail;
use App\Models\MedicalLeave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMedicalLeaveEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public MedicalLeave $medicalLeave,
        public string $email
    ) {
        // Establecer la cola usando el método del trait Queueable
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Enviando licencia médica por email', [
                'medical_leave_id' => $this->medicalLeave->id,
                'identifier' => $this->medicalLeave->identifier,
                'email' => $this->email,
            ]);

            Mail::to($this->email)
                ->send(new MedicalLeaveMail($this->medicalLeave));

            Log::info('Licencia médica enviada exitosamente', [
                'medical_leave_id' => $this->medicalLeave->id,
                'email' => $this->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al enviar licencia médica por email', [
                'medical_leave_id' => $this->medicalLeave->id,
                'email' => $this->email,
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
        Log::error('Job SendMedicalLeaveEmail falló después de múltiples intentos', [
            'medical_leave_id' => $this->medicalLeave->id,
            'email' => $this->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
