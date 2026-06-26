<?php

namespace App\Http\Middleware;

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
                    $actionType = $this->getActionType($resourceType);
                    $metadata = [
                        'endpoint' => $request->path(),
                        'method' => $request->method(),
                        'resource_type' => $resourceType ?? 'medical_history',
                    ];

                    // Log encounter separately if this is an encounter view
                    if ($resourceType === 'encounter') {
                        $encounterId = $request->route('encounter_id');
                        $this->auditService->logEncounterView(
                            patient: $patient,
                            encounterId: $encounterId,
                            metadata: $metadata
                        );
                    } else {
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
