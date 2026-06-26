<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientAccessLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PatientAccessAuditService
{
    /**
     * Log patient access (generic method)
     */
    public function logAccess(
        Patient $patient,
        string $actionType,
        string $resourceType = 'medical_history',
        ?array $metadata = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): PatientAccessLog {
        try {
            $user = Auth::user();

            // Skip logging if no authenticated user
            if (! $user) {
                return new PatientAccessLog;
            }

            $log = PatientAccessLog::create([
                'user_id' => $user->id,
                'patient_id' => $patient->id,
                'client_id' => $user->current_client_id ?? $user->clients()->first()?->id,
                'action_type' => $actionType,
                'resource_type' => $resourceType,
                'ip_address' => $ipAddress ?? request()->ip(),
                'user_agent' => $userAgent ?? request()->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => $metadata ?? [],
                'access_timestamp' => now(),
            ]);

            return $log;
        } catch (\Exception $e) {
            // Log error but don't throw - auditing failures shouldn't break user experience
            Log::error('Failed to log patient access', [
                'error' => $e->getMessage(),
                'patient_id' => $patient->id,
                'user_id' => Auth::id(),
            ]);

            return new PatientAccessLog;
        }
    }

    /**
     * Log medical history view
     */
    public function logMedicalHistoryView(Patient $patient, ?array $metadata = null): PatientAccessLog
    {
        return $this->logAccess(
            patient: $patient,
            actionType: 'view',
            resourceType: 'medical_history',
            metadata: $metadata ?? [
                'endpoint' => 'patient.medical_history',
                'timestamp' => now()->toIso8601String(),
            ]
        );
    }

    /**
     * Log history download
     */
    public function logHistoryDownload(
        Patient $patient,
        int $historyDownloadId,
        string $fileName,
        ?array $additionalMetadata = null
    ): PatientAccessLog {
        return $this->logAccess(
            patient: $patient,
            actionType: 'download',
            resourceType: 'complete_history',
            metadata: array_merge([
                'history_download_id' => $historyDownloadId,
                'filename' => $fileName,
                'download_timestamp' => now()->toIso8601String(),
            ], $additionalMetadata ?? [])
        );
    }

    /**
     * Log encounter view
     */
    public function logEncounterView(Patient $patient, int $encounterId, ?array $metadata = null): PatientAccessLog
    {
        return $this->logAccess(
            patient: $patient,
            actionType: 'view',
            resourceType: 'encounter',
            metadata: array_merge([
                'encounter_id' => $encounterId,
            ], $metadata ?? [])
        );
    }

    /**
     * Get patient audit summary
     */
    public function getPatientAuditSummary(Patient $patient, ?int $daysBack = 30): array
    {
        $since = now()->subDays($daysBack);

        $logs = PatientAccessLog::forPatient($patient->id)
            ->where('created_at', '>=', $since)
            ->get();

        return [
            'total_accesses' => $logs->count(),
            'unique_users' => $logs->pluck('user_id')->unique()->count(),
            'views' => $logs->where('action_type', 'view')->count(),
            'downloads' => $logs->where('action_type', 'download')->count(),
            'last_access' => $logs->max('access_timestamp'),
            'access_timeline' => $logs->groupBy(fn ($log) => $log->access_timestamp->toDateString())
                ->map(fn ($group) => $group->count()),
        ];
    }

    /**
     * Detect suspicious activity (high volume access in short time)
     */
    public function detectSuspiciousActivity(
        Patient $patient,
        int $threshold = 50,
        int $windowMinutes = 5
    ): bool {
        $recentAccess = PatientAccessLog::forPatient($patient->id)
            ->where('access_timestamp', '>=', now()->subMinutes($windowMinutes))
            ->count();

        return $recentAccess > $threshold;
    }

    /**
     * Get audit trail for a specific patient
     */
    public function getAuditTrail(
        Patient $patient,
        ?string $actionType = null,
        ?int $limit = 100
    ) {
        $query = PatientAccessLog::forPatient($patient->id);

        if ($actionType) {
            $query->forAction($actionType);
        }

        return $query
            ->with(['user', 'patient'])
            ->orderByDesc('access_timestamp')
            ->limit($limit)
            ->get();
    }

    /**
     * Export audit logs to array format
     */
    public function exportAuditLogs(
        ?int $clientId = null,
        ?int $patientId = null,
        ?string $actionType = null,
        ?string $fromDate = null,
        ?string $toDate = null
    ): array {
        $query = PatientAccessLog::query();

        if ($clientId) {
            $query->forClient($clientId);
        }

        if ($patientId) {
            $query->forPatient($patientId);
        }

        if ($actionType) {
            $query->forAction($actionType);
        }

        if ($fromDate && $toDate) {
            $query->dateRange($fromDate, $toDate);
        }

        return $query
            ->with(['user', 'patient'])
            ->orderByDesc('access_timestamp')
            ->get()
            ->map(fn ($log) => [
                'access_timestamp' => $log->access_timestamp->toIso8601String(),
                'user_name' => $log->user?->full_name ?? 'Unknown',
                'user_email' => $log->user?->email ?? 'N/A',
                'patient_name' => $log->patient?->name ?? 'Unknown',
                'action_type' => $log->action_type,
                'resource_type' => $log->resource_type,
                'ip_address' => $log->ip_address,
                'metadata' => json_encode($log->metadata),
            ])
            ->toArray();
    }
}
