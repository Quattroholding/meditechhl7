<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Condition extends Model
{
    use HasFactory;
    protected $fillable = [
        'fhir_id', 'patient_id', 'encounter_id', 'practitioner_id', 'identifier',
        'clinical_status', 'verification_status', 'code', 'category', 'severity',
        'onset_date', 'abatement_date', 'recorded_date', 'note', 'evidence',
        'extension'
    ];

    protected $casts = [
        'onset_date' => 'date',
        'abatement_date' => 'date',
        'recorded_date' => 'date',
        'evidence' => 'array',
        'extension' => 'array'
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

    public function icd10Code(): BelongsTo
    {
        return $this->belongsTo(Icd10Code::class,'code');
    }

    // Accesor para el recurso FHIR
    public function getFhirResourceAttribute()
    {
        return [
            'resourceType' => 'Condition',
            'id' => $this->fhir_id,
            'identifier' => [
                [
                    'system' => 'http://hospital.sistema/conditions',
                    'value' => $this->identifier
                ]
            ],
            'clinicalStatus' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/condition-clinical',
                        'code' => $this->clinical_status
                    ]
                ]
            ],
            'verificationStatus' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/condition-ver-status',
                        'code' => $this->verification_status
                    ]
                ]
            ],
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/condition-category',
                            'code' => $this->category
                        ]
                    ]
                ]
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://hl7.org/fhir/sid/icd-10',
                        'code' => $this->code,
                        'display' => $this->description
                    ]
                ]
            ],
            'subject' => [
                'reference' => 'Patient/' . $this->patient->fhir_id
            ],
            'encounter' => [
                'reference' => 'Encounter/' . ($this->encounter->fhir_id ?? '')
            ],
            'recordedDate' => $this->recorded_date->toIso8601String(),
        ];
    }

    // Accesor para descripción
    public function getDescriptionAttribute()
    {
        $conditions = collect($this->getCommonConditions());
        $found = $conditions->firstWhere('code', $this->code);
        return $found['description'] ?? 'Condición no especificada';
    }

    protected function getCommonConditions()
    {
        return [
            // La misma lista que en el factory
        ];
    }
}
