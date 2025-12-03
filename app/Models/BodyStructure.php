<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BodyStructure extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'fhir_id',
        'patient_id',
        'encounter_id',
        'identifier',
        'active',
        'morphology_code',
        'morphology_display',
        'location_code',
        'location_display',
        'location_qualifier_code',
        'location_qualifier_display',
        'description',
        'image_references',
        'extension',
        'included_structures',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'image_references' => 'array',
            'extension' => 'array',
            'included_structures' => 'array',
        ];
    }

    // Relationships

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function skinLesions(): HasMany
    {
        return $this->hasMany(SkinLesion::class);
    }

    public function markings(): HasMany
    {
        return $this->hasMany(BodySiteMarking::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'subject');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByLocation($query, string $locationCode)
    {
        return $query->where('location_code', $locationCode);
    }

    public function scopeByMorphology($query, string $morphologyCode)
    {
        return $query->where('morphology_code', $morphologyCode);
    }

    // Helper methods

    public function getFullLocationAttribute(): string
    {
        $location = $this->location_display;

        if ($this->location_qualifier_display) {
            $location = "{$this->location_qualifier_display} {$location}";
        }

        return $location;
    }

    public function toFHIR(): array
    {
        return [
            'resourceType' => 'BodyStructure',
            'id' => $this->fhir_id,
            'identifier' => [
                [
                    'system' => 'urn:meditech:bodystructure',
                    'value' => $this->identifier,
                ],
            ],
            'active' => $this->active,
            'morphology' => $this->morphology_code ? [
                'coding' => [
                    [
                        'system' => 'http://snomed.info/sct',
                        'code' => $this->morphology_code,
                        'display' => $this->morphology_display,
                    ],
                ],
            ] : null,
            'location' => [
                'coding' => [
                    [
                        'system' => 'http://snomed.info/sct',
                        'code' => $this->location_code,
                        'display' => $this->location_display,
                    ],
                ],
            ],
            'locationQualifier' => $this->location_qualifier_code ? [
                [
                    'coding' => [
                        [
                            'system' => 'http://snomed.info/sct',
                            'code' => $this->location_qualifier_code,
                            'display' => $this->location_qualifier_display,
                        ],
                    ],
                ],
            ] : null,
            'description' => $this->description,
            'image' => $this->image_references,
            'patient' => [
                'reference' => "Patient/{$this->patient->fhir_id}",
            ],
            'extension' => $this->extension,
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'body-structure-'.uniqid();
            }
        });
    }
}
