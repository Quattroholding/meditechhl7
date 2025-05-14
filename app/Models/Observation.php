<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Observation extends Model
{
    protected $fillable = ['patient_id', 'encounter_id', 'observation_type_id', 'value', 'unit', 'interpretation', 'category', 'observed_at', 'note'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }
    public function type(): BelongsTo { return $this->belongsTo(ObservationType::class, 'observation_type_id'); }
}
