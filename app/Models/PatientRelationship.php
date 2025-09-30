<?php

namespace App\Models;

use App\Models\Scopes\PatientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientRelationship extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'patient_id',
        'related_patient_id',
        'identifier',
        'identifier_type',
        'name',
        'given_name',
        'family_name',
        'gender',
        'birth_date',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'relationship_code',
        'relationship_display',
        'relationship_system',
        'is_emergency_contact',
        'is_insurance_subscriber',
        'contact_preferences',
        'effective_date',
        'end_date',
        'is_active',
        'notes',
        'extension',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_emergency_contact' => 'boolean',
        'is_insurance_subscriber' => 'boolean',
        'is_active' => 'boolean',
        'contact_preferences' => 'array',
        'extension' => 'array',
    ];

    // FHIR Relationship Codes Constants
    public const RELATIONSHIP_CODES = [
        'CHILD' => 'Child',
        'PARENT' => 'Parent',
        'SPOUSE' => 'Spouse',
        'DOMPART' => 'Domestic Partner',
        'SIBLING' => 'Sibling',
        'GRANDPRN' => 'Grandparent',
        'GRANDCHILD' => 'Grandchild',
        'AUNT' => 'Aunt',
        'UNCLE' => 'Uncle',
        'NIECE' => 'Niece',
        'NEPHEW' => 'Nephew',
        'COUSIN' => 'Cousin',
        'GGRPRN' => 'Great Grandparent',
        'GGRCHILD' => 'Great Grandchild',
        'INLAW' => 'In-law',
        'FRND' => 'Friend',
        'NEIGHBOR' => 'Neighbor',
        'ROOM' => 'Roommate',
        'GUARD' => 'Guardian',
        'NOK' => 'Next of Kin',
        'POWATT' => 'Power of Attorney',
        'ECON' => 'Emergency Contact',
        'O' => 'Other',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new PatientScope);
    }

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'patient_clients','patient_id');
    }

    public function relatedPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'related_patient_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeEmergencyContacts($query)
    {
        return $query->where('is_emergency_contact', true);
    }

    public function scopeInsuranceSubscribers($query)
    {
        return $query->where('is_insurance_subscriber', true);
    }

    public function scopeByRelationship($query, string $relationshipCode)
    {
        return $query->where('relationship_code', $relationshipCode);
    }

    // Accessors
    public function getFullNameAttribute(): ?string
    {
        if ($this->relatedPatient) {
            return $this->relatedPatient->name;
        }

        if ($this->given_name && $this->family_name) {
            return trim($this->given_name.' '.$this->family_name);
        }

        return $this->name;
    }

    public function getRelationshipDisplayAttribute($value): string
    {
        return $value ?: (self::RELATIONSHIP_CODES[$this->relationship_code] ?? $this->relationship_code);
    }

    // FHIR Resource
    public function getFhirResourceAttribute(): array
    {
        return [
            'resourceType' => 'RelatedPerson',
            'id' => $this->fhir_id,
            'patient' => [
                'reference' => "Patient/{$this->patient->fhir_id}",
                'display' => $this->patient->name,
            ],
            'relationship' => [
                [
                    'coding' => [
                        [
                            'system' => $this->relationship_system,
                            'code' => $this->relationship_code,
                            'display' => $this->relationship_display,
                        ],
                    ],
                ],
            ],
            'name' => [
                [
                    'use' => 'official',
                    'family' => $this->family_name ?: $this->relatedPatient?->family_name,
                    'given' => $this->given_name ? [$this->given_name] : ($this->relatedPatient ? [$this->relatedPatient->given_name] : []),
                ],
            ],
            'telecom' => array_filter([
                $this->phone ? [
                    'system' => 'phone',
                    'value' => $this->phone,
                    'use' => 'home',
                ] : null,
                $this->email ? [
                    'system' => 'email',
                    'value' => $this->email,
                ] : null,
            ]),
            'gender' => $this->gender ?: $this->relatedPatient?->gender,
            'birthDate' => $this->birth_date?->format('Y-m-d') ?: $this->relatedPatient?->birth_date?->format('Y-m-d'),
            'active' => $this->is_active,
            'period' => array_filter([
                'start' => $this->effective_date?->format('Y-m-d'),
                'end' => $this->end_date?->format('Y-m-d'),
            ]),
        ];
    }

    // Methods
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function isActive(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    public function getContactInfo(): array
    {
        return [
            'name' => $this->full_name,
            'phone' => $this->phone ?: $this->relatedPatient?->phone,
            'email' => $this->email ?: $this->relatedPatient?->email,
            'address' => $this->address ?: $this->relatedPatient?->address,
            'relationship' => $this->relationship_display,
            'is_emergency_contact' => $this->is_emergency_contact,
        ];
    }
}
