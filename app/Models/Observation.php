<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Observation extends Model
{
    protected $fillable = ['patient_id', 'encounter_id', 'practitioner_id', 'identifier', 'status', 'code',
        'category', 'value', 'value_string','unit','interpretation','note',
        'effective_date','issued_date','extension','reference_range','component'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }
    public function type(): BelongsTo { return $this->belongsTo(ObservationType::class, 'observation_type_id'); }

    public function vitalSign()
    {
        return $this->hasOne(VitalSign::class);
    }

    public function physicalExam()
    {
        return $this->hasOne(PhysicalExam::class);
    }
}
