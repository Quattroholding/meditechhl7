<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationRequest extends Model
{
    protected $fillable = [
        'fhir_id', 'encounter_id', 'patient_id', 'practitioner_id', 'medication_id', 'medication_id2',
        'identifier', 'status', 'intent', 'priority', 'reason', 'dosage_instruction',
        'dosage_text', 'route', 'frequency', 'quantity', 'refills', 'valid_from', 'duration',
        'valid_to', 'substitution_allowed', 'note', 'medication', 'narcotic', 'client_id', 'branch_id', 'consulting_room_id',
        'notification_sent_at',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'substitution_allowed' => 'boolean',
        'dosage_instruction' => 'array',
        'notification_sent_at' => 'datetime',
    ];

    // Relaciones
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medication_id');
    }

    public function medicina(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medication_id');
    }

    public function medication2(): BelongsTo
    {
        return $this->belongsTo(Medication::class, 'medication_id2');
    }

    public function getValidFromAttribute($attr)
    {
        return Carbon::parse($attr)->format('d-m-Y'); // Change the format to whichever you desire
    }

    public function getValidToAttribute($attr)
    {
        return Carbon::parse($attr)->format('d-m-Y'); // Change the format to whichever you desire
    }

    public function getStatusAttribute($attr)
    {
        switch ($attr) {
            case 'active':
                return '<div class="badge bg-primary">Activo</div>';
                break;
            case 'completed':
                return '<div class="badge bg-success">Completado</div>';
                break;
            case 'draft':
                return '<div class="badge bg-warning">Sin Confirmar</div>';
                break;
            case 'stopped':
                return '<div class="badge bg-secundary">Pausado</div>';
                break;
            case 'cancelled':
                return '<div class="badge bg-danger">Cancelado</div>';
                break;
            case 'on-hold':
                return '<div class="badge bg-warning">En Espera</div>';
                break;
        }
    }
}
