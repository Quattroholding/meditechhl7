<?php

namespace App\Jobs;

use App\Models\Patient;
use App\Services\ClaudeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateMedicalReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $patientId,
        private string $reportType
    ) {}

    public function handle(ClaudeService $claude): void
    {
        $patient = Patient::findOrFail($this->patientId);

        // Obtener datos del paciente
        $medicalHistory = $patient->medicalHistory;

        // Generar reporte con Claude
        $prompt = "Genera un reporte médico de tipo {$this->reportType} para este paciente...";

        $report = $claude->ask($prompt, [
            'max_tokens' => 4096,
            'temperature' => 0.2,
        ]);

        // Guardar el reporte
        $patient->reports()->create([
            'type' => $this->reportType,
            'content' => $report,
            'generated_at' => now(),
        ]);
    }
}
