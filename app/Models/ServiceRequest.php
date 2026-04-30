<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ServiceRequest extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['fhir_id', 'encounter_id', 'patient_id', 'practitioner_id', 'status', 'intent', 'priority', 'do_not_perform', 'code', 'service_type',
        'code_system', 'code_display', 'quantity', 'quantity_unit', 'occurrence_start', 'occurrence_end', 'body_site', 'note', 'patient_instruction',
        'supporting_info', 'reason_code', 'reason_reference', 'authored_on', 'last_updated', 'scb_id', 'notification_sent_at', 'message_control_id', 'hemo_identification',
        'performed_in_consultation', 'procedure_notes'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($serviceRequest) {
            if (empty($serviceRequest->hemo_identification)) {
                $serviceRequest->hemo_identification = self::generateUniqueHemoIdentification();
            }
        });
    }

    protected $casts = [
        'body_site' => 'array',
        'supporting_info' => 'array',
        'occurrence_start' => 'datetime',
        'occurrence_end' => 'datetime',
        'authored_on' => 'datetime',
        'last_updated' => 'datetime',
        'notification_sent_at' => 'datetime',
    ];

    // Relación con Encounter
    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    // Relación con Patient
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // Relación con Practitioner
    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    // Métodos útiles para estados HL7
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function cpt()
    {
        return $this->belongsTo(CptCode::class, 'code', 'code');
    }

    // Relación con ServiceRequestResult
    public function results(): HasMany
    {
        return $this->hasMany(ServiceRequestResult::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }

    /**
     * Cambiar estado automáticamente basado en lógica de negocio
     */
    public function autoChangeStatus($reason = null, $userId = null)
    {
        $newStatus = $this->determineAutoStatus();

        if ($newStatus && $newStatus !== $this->status) {
            $this->changeStatusTo($newStatus, $reason ?: __('service_request.auto_status_change'), 'automatic', $userId);
        }
    }

    /**
     * Determinar el estado automático basado en reglas de negocio
     */
    private function determineAutoStatus()
    {
        // Si tiene resultados y está activo, puede completarse automáticamente
        if ($this->status === 'active' && $this->results()->count() > 0) {
            // Lógica adicional: verificar si todos los resultados están completos
            $finalResults = $this->results()->where('status', 'final')->count();
            $totalResults = $this->results()->count();

            // Si todos los resultados son finales, marcar como completado
            if ($finalResults === $totalResults && $totalResults > 0) {
                return 'completed';
            }
        }

        // Si es borrador y no tiene estado específico, activar automáticamente
        if ($this->status === 'draft' || ! $this->status) {
            return 'active';
        }

        return null; // No cambiar estado
    }

    /**
     * Cambiar estado con validación y audit trail
     */
    public function changeStatusTo($newStatus, $reason = null, $changeType = 'manual', $userId = null)
    {
        if (! $this->isValidTransition($this->status, $newStatus)) {
            throw new \InvalidArgumentException(__('service_request.invalid_status_transition'));
        }

        $oldStatus = $this->status;

        // Registrar en el historial
        StatusHistoryLog::create([
            'model_type' => self::class,
            'model_id' => $this->id,
            'field_name' => 'status',
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
            'reason' => $reason,
            'change_type' => $changeType,
            'user_id' => $userId ?: Auth::id(),
            'changed_at' => now(),
        ]);

        // Actualizar el estado
        $this->update([
            'status' => $newStatus,
            'last_updated' => now(),
        ]);

        return true;
    }

    /**
     * Validar si una transición de estado es válida
     */
    private function isValidTransition($fromStatus, $toStatus)
    {
        $validTransitions = [
            null => ['draft', 'active'],
            'draft' => ['active', 'revoked', 'entered-in-error'],
            'active' => ['on-hold', 'completed', 'revoked', 'entered-in-error'],
            'on-hold' => ['active', 'revoked', 'entered-in-error'],
            'revoked' => ['entered-in-error'],
            'completed' => ['entered-in-error'],
            'entered-in-error' => [],
            'unknown' => ['draft', 'active', 'entered-in-error'],
        ];

        if (! $fromStatus) {
            return in_array($toStatus, ['draft', 'active']);
        }

        return in_array($toStatus, $validTransitions[$fromStatus] ?? []);
    }

    public function getOccurrenceStartAttribute($attr)
    {
        return Carbon::parse($attr)->format('d-m-Y'); // Change the format to whichever you desire
    }

    public function getOccrrenceEndAttribute($attr)
    {
        return Carbon::parse($attr)->format('d-m-Y'); // Change the format to whichever you desire
    }

    /**
     * Generate a unique 7-digit hemo identification number
     */
    public static function generateUniqueHemoIdentification(): string
    {
        $maxAttempts = 100;
        $attempts = 0;

        do {
            $hemoId = str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT);
            $exists = self::where('hemo_identification', $hemoId)->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);

        if ($exists) {
            throw new \RuntimeException('Unable to generate unique hemo identification after '.$maxAttempts.' attempts');
        }

        return $hemoId;
    }
}
