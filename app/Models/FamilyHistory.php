<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyHistory extends Model
{
    protected $fillable = ['patient_id', 'relationship', 'condition', 'note'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
}
