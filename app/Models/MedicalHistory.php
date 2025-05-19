<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MedicalHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'fhir_id', 'patient_id', 'category', 'title', 'description',
        'recorded_date', 'occurrence_date', 'clinical_status', 'verification_status'
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'occurrence_date' => 'date'
    ];

    // Relaciones
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function allergy(): HasOne
    {
        return $this->hasOne(Allergy::class);
    }

    public function procedure(): HasOne
    {
        return $this->hasOne(Procedure::class);
    }
}
