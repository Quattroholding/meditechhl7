<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Referral extends Model
{
    protected $fillable = [
        'fhir_id', 'encounter_id', 'patient_id', 'requester_id', 'referred_to_id','practitioner_id',
        'identifier', 'status', 'intent', 'priority', 'code', 'reason',
        'description', 'occurrence_date', 'note','supporting_info'
    ];

    protected $casts = [
        'occurrence_date' => 'date',
        'supporting_info' => 'array'
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

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'requester_id');
    }

    public function referredTo(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'referred_to_id');
    }

    public function speciality(){
        return $this->belongsTo(MedicalSpeciality::class,'code');
    }
}
