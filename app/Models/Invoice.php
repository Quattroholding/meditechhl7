<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = ['encounter_id', 'total_amount', 'status', 'issued_at', 'paid_at'];

    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }

}
