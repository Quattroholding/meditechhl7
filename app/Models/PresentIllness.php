<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresentIllness extends Model
{
    protected $fillable = [
        'fhir_id', 'encounter_id', 'patient_id', 'practitioner_id', 'description',
        'location', 'severity', 'duration','timing', 'onset', 'onset_date',
        'aggravating_factors', 'alleviating_factors', 'associated_symptoms','timeline'
    ];

    protected $casts = [
        'onset_date' => 'datetime',
        'aggravating_factors' => 'string',
        'alleviating_factors' => 'string',
        'associated_symptoms' => 'string',
        'timeline' => 'array'
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

    public function symptoms(): HasMany
    {
        return $this->hasMany(PresentIllnessSymptom::class);
    }
}
