<?php

namespace App\Jobs;

use App\Models\Encounter;
use App\Models\HistoryDownload;
use App\Models\Patient;
use App\Models\PatientPractitionerAuthorization;
use App\Models\PresentIllnesType;
use App\Models\Scopes\EncouterScope;
use App\Models\User;
use App\Notifications\PatientHistoryReadyNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class GeneratePatientHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutos

    public $tries = 3;

    public function __construct(
        public HistoryDownload $historyDownload,
        public Patient $patient,
        public User $requestedBy
    ) {}

    public function handle(): void
    {
        try {
            Log::info('Starting patient history generation', [
                'history_download_id' => $this->historyDownload->id,
                'patient_id' => $this->patient->id,
                'user_id' => $this->requestedBy->id,
            ]);

            // Determinar si el usuario tiene autorización completa
            $hasFullAuth = false;
            if ($this->requestedBy->hasRole('doctor') && $this->requestedBy->practitioner) {
                $hasFullAuth = PatientPractitionerAuthorization::where('patient_id', $this->patient->id)
                    ->where('practitioner_id', $this->requestedBy->practitioner->id)
                    ->exists();
            }

            // Obtener todos los encounters accesibles
            // IMPORTANTE: Los scopes globales no funcionan en Jobs (no hay auth()->user())
            // Por eso aplicamos el filtro manualmente según el rol
            $encounters = Encounter::withoutGlobalScope(EncouterScope::class)
                ->when($this->requestedBy->hasRole('doctor') && $this->requestedBy->practitioner && !$hasFullAuth, function ($query) {
                    // Doctor sin autorización completa: solo sus propios encounters
                    $query->where('practitioner_id', $this->requestedBy->practitioner->id);
                })
                ->when($this->requestedBy->hasRole('paciente') && $this->requestedBy->patient, function ($query) {
                    // Paciente: verificar que sea su propio historial (ya validado en controller)
                    // No aplicar filtro adicional, ya tiene acceso a todos sus encounters
                })
                ->where('patient_id', $this->patient->id)
                ->whereNotNull('appointment_id')
                ->with(['practitioner', 'medicalSpeciality', 'diagnoses.condition.icd10Code'])
                ->orderBy('end', 'desc')
                ->get();

            $totalEncounters = $encounters->count();

            if ($totalEncounters === 0) {
                $this->historyDownload->markAsFailed('No se encontraron consultas para este paciente.');

                return;
            }

            // Actualizar total
            $this->historyDownload->update(['total_encounters' => $totalEncounters]);

            // Crear directorio temporal
            $tempDir = storage_path('app/temp/history_'.$this->historyDownload->id);
            if (! is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $processedCount = 0;

            // Generar PDF para cada encounter
            foreach ($encounters as $index => $encounter) {
                try {
                    $this->generateEncounterPDF($encounter, $tempDir, $index + 1);
                    $processedCount++;

                    // Actualizar progreso cada 10 consultas
                    if ($processedCount % 10 === 0) {
                        $this->historyDownload->updateProgress($processedCount);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to generate PDF for encounter', [
                        'encounter_id' => $encounter->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Continuar con los demás
                }
            }

            // Actualizar progreso final
            $this->historyDownload->updateProgress($processedCount);

            // Crear ZIP
            $zipFileName = "historial_{$this->patient->id}_".now()->format('YmdHis').'.zip';
            // Usar directamente storage/app/history_downloads que tiene permisos correctos
            $zipPath = "history_downloads/{$zipFileName}";
            $fullZipPath = storage_path('app/'.$zipPath);

            // Asegurar que el directorio existe
            $zipDir = dirname($fullZipPath);
            if (! is_dir($zipDir)) {
                mkdir($zipDir, 0755, true);
            }

            $zip = new ZipArchive;
            if ($zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                // Agregar todos los PDFs al ZIP
                $files = glob($tempDir.'/*.pdf');
                foreach ($files as $file) {
                    $zip->addFile($file, basename($file));
                }

                // Agregar archivo README
                $readme = $this->generateReadme($encounters->count(), $processedCount);
                $zip->addFromString('LEEME.txt', $readme);

                $zip->close();

                // Limpiar archivos temporales
                foreach ($files as $file) {
                    unlink($file);
                }
                rmdir($tempDir);

                // Marcar como completado
                $this->historyDownload->markAsCompleted($zipPath);

                // Notificar al usuario
                $this->requestedBy->notify(new PatientHistoryReadyNotification($this->historyDownload));

                Log::info('Patient history generation completed', [
                    'history_download_id' => $this->historyDownload->id,
                    'total_encounters' => $totalEncounters,
                    'processed_encounters' => $processedCount,
                    'file_size' => filesize($fullZipPath),
                ]);
            } else {
                throw new \Exception('No se pudo crear el archivo ZIP');
            }

        } catch (\Exception $e) {
            Log::error('Patient history generation failed', [
                'history_download_id' => $this->historyDownload->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->historyDownload->markAsFailed($e->getMessage());

            throw $e;
        }
    }

    protected function generateEncounterPDF(Encounter $encounter, string $tempDir, int $index): void
    {
        // Preparar datos igual que en downloadResumen
        $data = $encounter;
        $data['consultation-list'] = PresentIllnesType::get();
        $consultation_disabilities = [];
        $lang = 'esp';
        $home_visit = false;

        // Buscar firma y sello del practitioner
        $sello = \App\Models\File::where('table_name', 'practitioners')
            ->where('record_id', $data->practitioner_id)
            ->where('type', 'seal')
            ->first()?->path ?? '';

        $firma = \App\Models\File::where('table_name', 'practitioners')
            ->where('record_id', $data->practitioner_id)
            ->where('type', 'signature')
            ->first()?->path ?? '';

        $mode = 'full';

        // Construir lista de diagnósticos
        foreach ($data->diagnoses as $d) {
            if ($d->condition) {
                if ($d->condition->icd10Code) {
                    $consultation_disabilities[] = '<td>'.$d->condition->code.'</td><td>'.$d->condition->icd10Code->description_es.'</td>';
                } else {
                    $consultation_disabilities[] = '<td>'.$d->condition->code.'</td><td>'.$d->condition->onset_info.'</td>';
                }
            }
        }

        // Generar PDF
        $pdf = Pdf::loadView('consultations.consultation_report.index', compact('data', 'lang', 'home_visit', 'sello', 'firma', 'mode', 'consultation_disabilities'));

        // Nombre del archivo con fecha y número
        $date = $encounter->end ? $encounter->end->format('Y-m-d') : now()->format('Y-m-d');
        $fileName = sprintf('%04d_%s_%s.pdf', $index, $date, $encounter->practitioner->name ?? 'desconocido');

        // Sanitizar nombre de archivo
        $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);

        // Guardar PDF
        $pdf->save($tempDir.'/'.$fileName);
    }

    protected function generateReadme(int $total, int $processed): string
    {
        return "HISTORIAL MÉDICO - {$this->patient->full_name}\n".
            "===============================================\n\n".
            'Fecha de generación: '.now()->format('d/m/Y H:i:s')."\n".
            "Total de consultas: {$total}\n".
            "Consultas procesadas: {$processed}\n".
            "Solicitado por: {$this->requestedBy->full_name}\n\n".
            "Los archivos están numerados y ordenados cronológicamente (más reciente primero).\n".
            "Formato: NNNN_FECHA_MEDICO.pdf\n\n".
            "Este archivo expira en 24 horas desde su generación.\n";
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GeneratePatientHistoryJob failed permanently', [
            'history_download_id' => $this->historyDownload->id,
            'error' => $exception->getMessage(),
        ]);

        $this->historyDownload->markAsFailed($exception->getMessage());
    }
}
