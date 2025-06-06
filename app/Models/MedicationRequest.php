<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class,'medication_id');
    }

    public function getValidFromAttribute($attr) {
        return Carbon::parse($attr)->format('d-m-Y'); //Change the format to whichever you desire
    }

    public function getValidToAttribute($attr) {
        return Carbon::parse($attr)->format('d-m-Y'); //Change the format to whichever you desire
    }
}
