<?php

namespace App\Models;

class RapidAccess extends BaseModel
{
    protected $table = 'rapid_access';

    protected $fillable = ['type', 'client_id', 'user_id', 'cpt_id', 'medicine_id', 'medication_id', 'active', 'encounter_section_id'];

    public function consultationField()
    {
        return $this->belongsTo(ConsultationField::class);
    }

    public function cpt()
    {
        return $this->belongsTo(CptCode::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}
