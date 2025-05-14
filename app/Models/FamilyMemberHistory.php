<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMemberHistory extends Model
{
    protected $fillable = ['patient_id', 'family_relationship_id', 'condition', 'onset_age', 'deceased', 'note'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function relationship(): BelongsTo { return $this->belongsTo(FamilyRelationship::class, 'family_relationship_id'); }
}
