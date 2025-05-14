<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Symptom extends Model
{
    protected $fillable = ['encounter_id', 'symptom', 'severity', 'duration', 'onset', 'modifying_factors'];

    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }
}
