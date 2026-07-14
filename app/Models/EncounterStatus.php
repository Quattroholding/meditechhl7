<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncounterStatus extends BaseModel
{
    protected $table = 'encounter_status';

    protected $fillable = ['encounter_id', 'user_id', 'status', 'previous_status', 'observation'];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
