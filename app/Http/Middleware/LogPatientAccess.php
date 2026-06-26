<?php

namespace App\Http\Middleware;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Patient;
use App\Services\PatientAccessAuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogPatientAccess
{
    public function __construct(private PatientAccessAuditService $auditService) {}

    public function handle(Request $request, Closure $next, ?string $resourceType = null)
    {
        $response = $next($request);

        // Only log successful responses (2xx status codes)
        if ($response->status() >= 200 && $response->status() < 300) {
            try {
                $patient = $this->extractPatient($request);

                if ($patient) {
                    $metadata = [
                        'endpoint' => $request->path(),
                        'method' => $request->method(),
                        'resource_type' => $resourceType ?? 'medical_history',
                    ];

                    // Log encounter view
                    if ($resourceType === 'encounter') {
                        $encounterId = $request->route('encounter_id');
                        $this->auditService->logEncounterView(
                            patient: $patient,
                            encounterId: $encounterId,
                            metadata: $metadata
                        );
                    }
                    // Log encounter download (resumen/summary PDF)
                    elseif ($resourceType === 'encounter_download') {
                        $appointmentId = $request->route('appointment_id');
                        $encounter = Encounter::where('appointment_id', $appointmentId)->first();
                        if ($encounter) {
                            $this->auditService->logEncounterDownload(
                                patient: $patient,
                                encounterId: $encounter->id,
                                fileName: 'resumen_consulta_'.$patient->id.'.pdf',
                                additionalMetadata: $metadata
                            );
                        }
                    }
                    // Log medical history view
                    else {
                        $this->auditService->logMedicalHistoryView(
                            patient: $patient,
                            metadata: $metadata
                        );
                    }
                }
            } catch (\Exception $e) {
                // Silently fail - don't disrupt user experience if logging fails
                Log::warning(
                    'Failed to log patient access in middleware',
                    ['error' => $e->getMessage()]
                );
            }
        }

        return $response;
    }

    /**
     * Extract patient from request route parameters
     */
    private function extractPatient(Request $request): ?Patient
    {
        // Try common parameter names
        $patientId = $request->route('id')
            ?? $request->route('patient')
            ?? $request->route('patient_id');

        if ($patientId) {
            if ($patientId instanceof Patient) {
                return $patientId;
            }

            return Patient::find($patientId);
        }

        // Try to extract patient from encounter
        $encounterId = $request->route('encounter_id');
        if ($encounterId) {
            $encounter = Encounter::find($encounterId);
            if ($encounter) {
                return $encounter->patient;
            }
        }

        // Try to extract patient from appointment
        $appointmentId = $request->route('appointment_id');
        if ($appointmentId) {
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                return $appointment->patient;
            }
        }

        return null;
    }

    /**
     * Determine action type based on resource type
     */
    private function getActionType(?string $resourceType): string
    {
        return match ($resourceType) {
            'encounter' => 'view',
            'medical_history' => 'view',
            'download' => 'download',
            'export' => 'export',
            default => 'view',
        };
    }
}
