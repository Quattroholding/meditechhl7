<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncounterSnapshot extends Model
{
    protected $fillable = [
        'encounter_id',
        'version',
        'snapshot_type',
        'snapshot_data',
        'change_summary',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_data' => 'array',
        ];
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
