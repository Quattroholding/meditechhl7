<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allergy extends Model
{
    protected $fillable = [
        'medical_history_id', 'code', 'substance', 'type', 'category',
        'criticality', 'reaction_severity', 'reaction_manifestation',
        'reaction_description', 'reaction_onset', 'note'
    ];

    protected $casts = [
        'reaction_onset' => 'date'
    ];

    // Relaciones
    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'medical_history_id');
    }
}
