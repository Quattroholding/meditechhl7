<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ClinicalImpression extends Model
{
    protected $fillable = ['patient_id','encounter_id','practitioner_id', 'status', 'description', 'fhir_id'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }
    public function practitioner(): BelongsTo { return $this->belongsTo(Practitioner::class,'practitioner_id'); }
}
