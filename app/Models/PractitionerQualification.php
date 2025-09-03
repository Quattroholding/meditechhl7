<?php

namespace App\Models;

class PractitionerQualification extends BaseModel
{
    protected $fillable = ['code', 'system', 'display', 'period_start', 'period_end', 'medical_speciality_id', 'default'];

    public function practitioner()
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function medicalSpeciality()
    {
        return $this->belongsTo(MedicalSpeciality::class);
    }
}
