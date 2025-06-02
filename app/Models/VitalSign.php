<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class VitalSign  extends Model
{
    protected $fillable = [
        'fhir_id', 'encounter_id', 'patient_id', 'practitioner_id', 'status',
        'code', 'category', 'value', 'unit', 'interpretation', 'note',
        'effective_date', 'issued_date', 'body_site', 'method'
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'issued_date' => 'datetime',
        'value' => 'decimal:2'
    ];

    // Relaciones
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function observationType()
    {
        return $this->belongsTo(ClinicalObservationType::class, 'code', 'code');
    }

    // Obtener el código LOINC del signo vital
    public function getLoincCodeAttribute(): ?string
    {
        $code = $this->code;

        if (!$code || !isset($code['coding'])) {
            return null;
        }

        $loincCoding = collect($code['coding'])
            ->where('system', 'http://loinc.org')
            ->first();

        return $loincCoding['code'] ?? null;
    }

    // Obtener el nombre display del signo vital
    public function getDisplayNameAttribute(): string
    {
        $code = $this->code;

        if (!$code) {
            return 'N/A';
        }

        // Primero buscar en coding
        if (isset($code['coding']) && is_array($code['coding'])) {
            $coding = collect($code['coding'])->first();
            if ($coding && isset($coding['display'])) {
                return $coding['display'];
            }
        }

        // Fallback al text
        return $code['text'] ?? 'N/A';
    }

    // Obtener el valor numérico principal
    public function getNumericValueAttribute(): ?float
    {
        $value = $this->value;

        if (!$value) {
            return null;
        }

        // Si es un Quantity
        if (isset($value['value']) && is_numeric($value['value'])) {
            return (float) $value['value'];
        }

        // Si es valor directo
        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    // Obtener la unidad de medida
    public function getUnitAttribute(): ?string
    {
        $value = $this->value;

        if (!$value || !isset($value['unit'])) {
            return null;
        }

        return $value['unit'];
    }

    // Obtener el valor formateado con unidad
    public function getFormattedValueAttribute(): string
    {
        $numericValue = $this->value;
        $unit = $this->unit;

        if ($numericValue === null) {
            return 'N/A';
        }

        $formatted = number_format($numericValue, 1);

        return $unit ? "{$formatted} {$unit}" : $formatted;
    }

    // Obtener componentes para observaciones complejas (como presión arterial)
    public function getComponentsAttribute(): array
    {
        if (!$this->component || !is_array($this->component)) {
            return [];
        }

        return collect($this->component)->map(function ($comp) {
            $code = $comp['code'] ?? null;
            $value = $comp['valueQuantity'] ?? null;

            $display = 'N/A';
            if ($code && isset($code['coding'])) {
                $coding = collect($code['coding'])->first();
                $display = $coding['display'] ?? ($code['text'] ?? 'N/A');
            }

            $numericValue = null;
            $unit = null;
            if ($value && isset($value['value'])) {
                $numericValue = (float) $value['value'];
                $unit = $value['unit'] ?? null;
            }

            return [
                'display' => $display,
                'value' => $numericValue,
                'unit' => $unit,
                'formatted' => $numericValue !== null
                    ? number_format($numericValue, 0) . ($unit ? " {$unit}" : '')
                    : 'N/A'
            ];
        })->toArray();
    }

    // Determinar si el valor está en rango normal
    public function getIsNormalAttribute(): bool
    {
        $referenceRange = $this->reference_range;
        $value = $this->numeric_value;

        if (!$referenceRange || !$value || !isset($referenceRange[0])) {
            return true; // Asumimos normal si no hay rango definido
        }

        $range = $referenceRange[0];
        $low = $range['low']['value'] ?? null;
        $high = $range['high']['value'] ?? null;

        if ($low !== null && $value < $low) {
            return false;
        }

        if ($high !== null && $value > $high) {
            return false;
        }

        return true;
    }

    // Obtener el estado de riesgo
    public function getRiskLevelAttribute(): string
    {
        // Primero verificar interpretación FHIR
        if ($this->interpretation) {
            switch (strtolower($this->interpretation)) {
                case 'h':
                case 'hh':
                case 'high':
                case 'critical':
                    return 'high';
                case 'l':
                case 'll':
                case 'low':
                    return 'low';
                case 'n':
                case 'normal':
                    return 'normal';
                default:
                    break;
            }
        }

        // Fallback a comparación con rango normal
        return $this->is_normal ? 'normal' : 'abnormal';
    }

    // Scopes para filtrar por tipo de signo vital
    public function scopeVitalSigns($query)
    {
        return $query->whereJsonContains('category', [
            ['coding' => [['code' => 'vital-signs']]]
        ]);
    }

    public function scopeByLoincCode($query, string $loincCode)
    {
        return $query->whereJsonContains('code', [
            'coding' => [['system' => 'http://loinc.org', 'code' => $loincCode]]
        ]);
    }

    public function scopeForPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('effective_date', '>=', Carbon::now()->subDays($days));
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('effective_date', 'desc');
    }
}
