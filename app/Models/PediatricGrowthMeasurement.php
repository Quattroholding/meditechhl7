<?php

namespace App\Models;

use App\Services\WHOGrowthCalculator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PediatricGrowthMeasurement extends BaseModel
{
    protected $fillable = [
        'fhir_id',
        'patient_id',
        'encounter_id',
        'practitioner_id',
        'client_id',
        'branch_id',
        'consulting_room_id',
        'measurement_date',
        'age_months',
        'gender',
        'weight_kg',
        'height_cm',
        'head_circumference_cm',
        'bmi',
        'weight_for_age_percentile',
        'weight_for_age_zscore',
        'height_for_age_percentile',
        'height_for_age_zscore',
        'bmi_for_age_percentile',
        'bmi_for_age_zscore',
        'head_circ_for_age_percentile',
        'head_circ_for_age_zscore',
        'status',
        'nutritional_status',
        'fhir_component',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'measurement_date' => 'date',
            'age_months' => 'integer',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:1',
            'head_circumference_cm' => 'decimal:1',
            'bmi' => 'decimal:2',
            'weight_for_age_percentile' => 'decimal:2',
            'weight_for_age_zscore' => 'decimal:2',
            'height_for_age_percentile' => 'decimal:2',
            'height_for_age_zscore' => 'decimal:2',
            'bmi_for_age_percentile' => 'decimal:2',
            'bmi_for_age_zscore' => 'decimal:2',
            'head_circ_for_age_percentile' => 'decimal:2',
            'head_circ_for_age_zscore' => 'decimal:2',
            'fhir_component' => 'array',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'observation-growth-'.Str::uuid();
            }

            $model->normalizeGender();
            $model->calculateBMI();
            $model->calculatePercentiles();
            $model->assessNutritionalStatus();
        });

        static::updating(function ($model) {
            $model->normalizeGender();
            $model->calculateBMI();
            $model->calculatePercentiles();
            $model->assessNutritionalStatus();
        });
    }

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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function consultingRoom(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class);
    }

    protected function normalizeGender(): void
    {
        if (! $this->gender && $this->patient) {
            $patientGender = strtolower($this->patient->gender ?? '');
            if (in_array($patientGender, ['masculino', 'male', 'm'])) {
                $this->gender = 'male';
            } elseif (in_array($patientGender, ['femenino', 'female', 'f'])) {
                $this->gender = 'female';
            }
        }
    }

    public function calculateBMI(): void
    {
        if ($this->weight_kg && $this->height_cm) {
            $heightM = $this->height_cm / 100;
            $this->bmi = round($this->weight_kg / ($heightM * $heightM), 2);
        }
    }

    public function calculatePercentiles(): void
    {
        if (! $this->gender || ! $this->age_months) {
            return;
        }

        $calculator = new WHOGrowthCalculator;

        if ($this->weight_kg) {
            $result = $calculator->calculate('weight_for_age', $this->gender, $this->age_months, $this->weight_kg);
            $this->weight_for_age_percentile = $result['percentile'];
            $this->weight_for_age_zscore = $result['zscore'];
        }

        if ($this->height_cm) {
            $result = $calculator->calculate('height_for_age', $this->gender, $this->age_months, $this->height_cm);
            $this->height_for_age_percentile = $result['percentile'];
            $this->height_for_age_zscore = $result['zscore'];
        }

        if ($this->bmi) {
            $result = $calculator->calculate('bmi_for_age', $this->gender, $this->age_months, $this->bmi);
            $this->bmi_for_age_percentile = $result['percentile'];
            $this->bmi_for_age_zscore = $result['zscore'];
        }

        if ($this->head_circumference_cm && $this->age_months <= 24) {
            $result = $calculator->calculate('head_circumference_for_age', $this->gender, $this->age_months, $this->head_circumference_cm);
            $this->head_circ_for_age_percentile = $result['percentile'];
            $this->head_circ_for_age_zscore = $result['zscore'];
        }
    }

    public function assessNutritionalStatus(): void
    {
        $status = [];

        if ($this->weight_for_age_zscore !== null) {
            if ($this->weight_for_age_zscore < -2) {
                $status[] = 'underweight';
            } elseif ($this->weight_for_age_zscore > 2) {
                $status[] = 'overweight';
            }
        }

        if ($this->height_for_age_zscore !== null) {
            if ($this->height_for_age_zscore < -2) {
                $status[] = 'stunted';
            }
        }

        if ($this->bmi_for_age_zscore !== null) {
            if ($this->bmi_for_age_zscore < -2) {
                $status[] = 'wasted';
            } elseif ($this->bmi_for_age_zscore > 2) {
                $status[] = 'obese';
            }
        }

        if (empty($status)) {
            $this->nutritional_status = 'normal';
        } else {
            $this->nutritional_status = implode(', ', $status);
        }
    }

    public function toFHIR(): array
    {
        $components = [];

        if ($this->weight_kg) {
            $components[] = [
                'code' => [
                    'coding' => [
                        [
                            'system' => 'http://loinc.org',
                            'code' => '29463-7',
                            'display' => 'Body Weight',
                        ],
                    ],
                ],
                'valueQuantity' => [
                    'value' => $this->weight_kg,
                    'unit' => 'kg',
                    'system' => 'http://unitsofmeasure.org',
                    'code' => 'kg',
                ],
            ];
        }

        if ($this->height_cm) {
            $components[] = [
                'code' => [
                    'coding' => [
                        [
                            'system' => 'http://loinc.org',
                            'code' => $this->age_months < 24 ? '8306-3' : '8302-2',
                            'display' => $this->age_months < 24 ? 'Body Height (lying)' : 'Body Height',
                        ],
                    ],
                ],
                'valueQuantity' => [
                    'value' => $this->height_cm,
                    'unit' => 'cm',
                    'system' => 'http://unitsofmeasure.org',
                    'code' => 'cm',
                ],
            ];
        }

        if ($this->head_circumference_cm) {
            $components[] = [
                'code' => [
                    'coding' => [
                        [
                            'system' => 'http://loinc.org',
                            'code' => '9843-4',
                            'display' => 'Head Circumference',
                        ],
                    ],
                ],
                'valueQuantity' => [
                    'value' => $this->head_circumference_cm,
                    'unit' => 'cm',
                    'system' => 'http://unitsofmeasure.org',
                    'code' => 'cm',
                ],
            ];
        }

        if ($this->bmi) {
            $components[] = [
                'code' => [
                    'coding' => [
                        [
                            'system' => 'http://loinc.org',
                            'code' => '39156-5',
                            'display' => 'BMI',
                        ],
                    ],
                ],
                'valueQuantity' => [
                    'value' => $this->bmi,
                    'unit' => 'kg/m2',
                    'system' => 'http://unitsofmeasure.org',
                    'code' => 'kg/m2',
                ],
            ];
        }

        return [
            'resourceType' => 'Observation',
            'id' => $this->fhir_id,
            'status' => $this->status,
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/observation-category',
                            'code' => 'vital-signs',
                            'display' => 'Vital Signs',
                        ],
                    ],
                ],
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => '85354-9',
                        'display' => 'Pediatric Growth Measurements',
                    ],
                ],
            ],
            'subject' => [
                'reference' => 'Patient/'.$this->patient->fhir_id,
                'display' => $this->patient->full_name,
            ],
            'encounter' => $this->encounter_id ? [
                'reference' => 'Encounter/'.$this->encounter->fhir_id,
            ] : null,
            'effectiveDateTime' => $this->measurement_date->toIso8601String(),
            'issued' => $this->created_at->toIso8601String(),
            'performer' => [
                [
                    'reference' => 'Practitioner/'.$this->practitioner->fhir_id,
                    'display' => $this->practitioner->full_name,
                ],
            ],
            'component' => $components,
            'note' => $this->note ? [
                ['text' => $this->note],
            ] : null,
        ];
    }
}
