<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Procedure extends Model
{
    protected $fillable = [
        'fhir_id',
        'encounter_id',
        'patient_id',
        'practitioner_id',
        'code',
        'display',
        'status',
        'performed_date',
        'reason',
        'outcome',
        'complication',
        'follow_up'
    ];

    protected $casts = [
        'performed_date' => 'date',
        'complication' => 'array',
        'follow_up' => 'array'
    ];

    // Relaciones
    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }
}
