<?php

namespace App\Models\Recepy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecepyPrescriptionMedication extends Model
{
    use SoftDeletes;

    protected $table = 'recepy_prescription_medications';

    protected $fillable = [
        'prescription_id',
        'medication_name',
        'presentation',
        'concentration',
        'dosage',
        'frequency',
        'duration',
        'instructions',
        'quantity',
        'line_order',
        'is_active',
        'administration_route',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'line_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(RecepyPrescription::class, 'prescription_id');
    }

    public function getFullMedicationDescriptionAttribute(): string
    {
        $parts = [$this->medication_name];

        if ($this->presentation) {
            $parts[] = $this->presentation;
        }

        if ($this->concentration) {
            $parts[] = $this->concentration;
        }

        return implode(' - ', $parts);
    }

    public function getFullInstructionsAttribute(): string
    {
        $parts = [];

        if ($this->administration_route) {
            $parts[] = $this->administration_route;
        }
        if ($this->dosage) {
            $parts[] = $this->dosage;
        }

        if ($this->frequency) {
            $parts[] = $this->frequency;
        }

        if ($this->duration) {
            $parts[] = 'por '.$this->duration;
        }

        $instruction = implode(', ', $parts);

        if ($this->instructions) {
            $instruction .= '. '.$this->instructions;
        }

        return ucfirst($instruction);
    }
}
