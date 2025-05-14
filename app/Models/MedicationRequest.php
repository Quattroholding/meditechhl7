<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MedicationRequest extends Model
{
    protected $fillable = [
        'fhir_id', 'encounter_id', 'patient_id', 'practitioner_id', 'medication_id',
        'identifier', 'status', 'intent', 'priority', 'reason', 'dosage_instruction',
        'dosage_text', 'route', 'frequency', 'quantity', 'refills', 'valid_from',
        'valid_to', 'substitution_allowed', 'note'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'substitution_allowed' => 'boolean',
        'dosage_instruction'=>'array',
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

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
