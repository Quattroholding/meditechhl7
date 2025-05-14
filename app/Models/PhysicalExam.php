<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalExam extends Model
{
    protected $fillable = [
        'fhir_id', 'encounter_id', 'patient_id', 'practitioner_id', 'status',
        'category', 'code', 'description', 'method', 'conclusion', 'effective_date'
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'body_site' => 'array',
        'finding' => 'array',
        'media' => 'array'
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

    public function observationType()
    {
        return $this->belongsTo(ClinicalObservationType::class, 'code', 'code');
    }
}
