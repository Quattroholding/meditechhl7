<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationIngredient extends Model
{
    protected $fillable = [
        'medication_id',
        'substance_code',
        'substance_display',
        'strength_value',
        'strength_unit',
    ];

    protected function casts(): array
    {
        return [
            'strength_value' => 'decimal:2',
        ];
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
