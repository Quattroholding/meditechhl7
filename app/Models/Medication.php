<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    protected $fillable = [
        'fhir_id',
        'code',
        'name',
        'form',
        'dose',
        'strength',
        'ingredient',
        'manufacturer',
        'is_brand',
        'is_over_the_counter',
        'status',
        'product_type',
    ];

    protected $casts = [
        'ingredient' => 'array',
        'is_brand' => 'boolean',
        'is_over_the_counter' => 'boolean'
    ];

    public function medicationRequests(): HasMany
    {
        return $this->hasMany(MedicationRequest::class);
    }

    // Accesor para FHIR
    public function getFhirResourceAttribute()
    {
        return [
            'resourceType' => 'Medication',
            'id' => $this->fhir_id,
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://www.nlm.nih.gov/research/umls/rxnorm',
                        'code' => $this->code,
                        'display' => $this->name
                    ]
                ]
            ],
            'status' => $this->status,
            'form' => [
                'coding' => [
                    [
                        'system' => 'http://snomed.info/sct',
                        'code' => $this->formToCode(),
                        'display' => $this->form
                    ]
                ]
            ],
            'ingredient' => array_map(function ($ing) {
                return [
                    'item' => [
                        'reference' => 'Substance/' . Str::slug($ing['item']),
                        'display' => $ing['item']
                    ],
                    'strength' => [
                        'numerator' => [
                            'value' => (float) explode(' ', $ing['strength'])[0],
                            'unit' => explode(' ', $ing['strength'])[1],
                            'system' => 'http://unitsofmeasure.org',
                            'code' => $this->unitToUcum(explode(' ', $ing['strength'])[1])
                        ]
                    ]
                ];
            }, $this->ingredient),
            'isBrand' => $this->is_brand,
            'overTheCounter' => $this->is_over_the_counter
        ];
    }

    protected function formToCode(): string
    {
        $forms = [
            'Tableta' => '385055001',
            'Cápsula' => '385023001',
            'Inyección' => '385219001',
            'Crema' => '385218004',
            'Jarabe' => '385214008',
            'Inhalador' => '421637006',
            'Gotas' => '385215009'
        ];

        return $forms[$this->form] ?? '385055001'; // Default to tablet
    }

    protected function unitToUcum(string $unit): string
    {
        $units = [
            'mg' => 'mg',
            'mL' => 'mL',
            '%' => '%',
            'mcg' => 'ug',
            'mg/5mL' => 'mg/5mL'
        ];

        return $units[$unit] ?? '1';
    }
}
