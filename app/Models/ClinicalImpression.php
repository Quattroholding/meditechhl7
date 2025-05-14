<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ClinicalImpression extends Model
{
    protected $fillable = ['patient_id','encounter_id','author_id', 'type', 'note', 'recorded_at'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }
    public function author(): BelongsTo { return $this->belongsTo(User::class,'author_id'); }
}
