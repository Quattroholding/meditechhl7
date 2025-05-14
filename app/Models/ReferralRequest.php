<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReferralRequest extends Model
{
    protected $fillable = ['encounter_id', 'referred_to_practitioner_id', 'reason', 'status', 'requested_at'];

    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }
    public function referredTo(): BelongsTo { return $this->belongsTo(Practitioner::class, 'referred_to_practitioner_id'); }
}
