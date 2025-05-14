<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SocialHistory extends Model
{
    protected $fillable = ['patient_id', 'encounter_id', 'social_history_type_id', 'description', 'recorded_at'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }
    public function type(): BelongsTo { return $this->belongsTo(SocialHistoryType::class, 'social_history_type_id'); }
}
