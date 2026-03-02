<?php

namespace App\Http\Controllers;

use App\Jobs\GeneratePatientHistoryJob;
use App\Models\HistoryDownload;
use App\Models\Patient;
use Illuminate\Support\Facades\Storage;

class PatientHistoryController extends Controller
{
    /**
     * Solicitar generación de historial completo
     */
    public function generate(Patient $patient)
    {
        try {
            // Verificar que el usuario tenga acceso al paciente
            $user = auth()->user();


            // Admins siempre tienen acceso
            if (! $user->hasRole('admin')) {
                // Para doctores, verificar que tengan consultas o autorización con este paciente

                if ($user->hasRole('doctor') && $user->practitioner) {
                    $practitionerId = $user->practitioner->id;

                    // Verificar si tiene consultas con el paciente
                    $hasEncounters = \App\Models\Encounter::where('patient_id', $patient->id)
                        ->where('practitioner_id', $practitionerId)
                        ->exists();

                    // Verificar si tiene autorización explícita del paciente
                    $hasAuthorization = \App\Models\PatientPractitionerAuthorization::where('patient_id', $patient->id)
                        ->where('practitioner_id', $practitionerId)
                        ->exists();

                    if (! $hasEncounters && ! $hasAuthorization) {
                        abort(403, 'No tiene permiso para ver este paciente');
                    }
                } elseif ($user->hasRole('paciente')) {
                    // Si es paciente, verificar que sea su propio historial
                    if ($patient->user_id !== $user->id) {
                        abort(403, 'No tiene permiso para ver este paciente');
                    }
                } else {
                    // Otros roles no tienen acceso
                    abort(403, 'No tiene permiso para ver este paciente');
                }
            }

            // Verificar si ya hay una descarga en proceso
            $existingDownload = HistoryDownload::where('patient_id', $patient->id)
                ->where('requested_by_user_id', auth()->id())
                ->where('status', 'processing')
                ->first();

            if ($existingDownload) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ya hay una descarga en proceso para este paciente',
                    'history_download_id' => $existingDownload->id,
                    'status' => 'processing',
                    'progress' => $existingDownload->getProgressPercentage(),
                ]);
            }

            // Crear registro de descarga
            $historyDownload = HistoryDownload::create([
                'patient_id' => $patient->id,
                'requested_by_user_id' => auth()->id(),
                'status' => 'processing',
                'total_encounters' => 0,
                'processed_encounters' => 0,
            ]);

            // Disparar el job
            GeneratePatientHistoryJob::dispatch($historyDownload, $patient, auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Generación de historial iniciada. Recibirás una notificación cuando esté listo.',
                'history_download_id' => $historyDownload->id,
                'status' => 'processing',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar la generación: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar estado de generación
     */
    public function status($id)
    {
        try {
            $historyDownload = HistoryDownload::findOrFail($id);

            // Verificar que el usuario sea el solicitante
            if ($historyDownload->requested_by_user_id !== auth()->id()) {
                abort(403);
            }

            return response()->json([
                'success' => true,
                'status' => $historyDownload->status,
                'total_encounters' => $historyDownload->total_encounters,
                'processed_encounters' => $historyDownload->processed_encounters,
                'progress' => $historyDownload->getProgressPercentage(),
                'completed_at' => $historyDownload->completed_at?->toIso8601String(),
                'download_url' => $historyDownload->isCompleted() ? $historyDownload->getDownloadUrl() : null,
                'error_message' => $historyDownload->error_message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar estado: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Descargar historial generado
     */
    public function download($id)
    {
        try {
            $historyDownload = HistoryDownload::findOrFail($id);

            // Verificar que el usuario sea el solicitante
            if ($historyDownload->requested_by_user_id !== auth()->id()) {
                abort(403, 'No tiene permiso para descargar este archivo');
            }

            // Verificar que esté completado
            if (! $historyDownload->isCompleted()) {
                return redirect()->back()->with('message.error', 'El historial aún no está listo');
            }

            // Verificar que no haya expirado
            if ($historyDownload->isExpired()) {
                return redirect()->back()->with('message.error', 'Este archivo ha expirado');
            }

            // Verificar que el archivo existe
            $fullPath = storage_path('app/'.$historyDownload->file_path);
            if (! file_exists($fullPath)) {
                return redirect()->back()->with('message.error', 'El archivo no se encuentra disponible');
            }

            // Descargar archivo
            $fileName = 'historial_'.$historyDownload->patient->full_name.'_'.now()->format('Ymd').'.zip';

            return response()->download($fullPath, $fileName)->deleteFileAfterSend(false);

        } catch (\Exception $e) {
            return redirect()->back()->with('message.error', 'Error al descargar: '.$e->getMessage());
        }
    }

    /**
     * Cancelar generación en proceso
     */
    public function cancel($id)
    {
        try {
            $historyDownload = HistoryDownload::findOrFail($id);

            // Verificar que el usuario sea el solicitante
            if ($historyDownload->requested_by_user_id !== auth()->id()) {
                abort(403);
            }

            // Solo se puede cancelar si está en proceso
            if ($historyDownload->isProcessing()) {
                $historyDownload->markAsFailed('Cancelado por el usuario');

                return response()->json([
                    'success' => true,
                    'message' => 'Generación cancelada',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar en el estado actual',
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar: '.$e->getMessage(),
            ], 500);
        }
    }
}
