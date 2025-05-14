<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class VitalSign  extends Model
{
    protected $fillable = [
        'fhir_id', 'encounter_id', 'patient_id', 'practitioner_id', 'status',
        'code', 'category', 'value', 'unit', 'interpretation', 'note',
        'effective_date', 'issued_date', 'body_site', 'method'
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'issued_date' => 'datetime',
        'value' => 'decimal:2'
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
