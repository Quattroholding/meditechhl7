<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientInsurancePolicy extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'insurance_company_id',
        'policy_number',
        'group_number',
        'subscriber_id',
        'subscriber_name',
        'subscriber_patient_id',
        'relationship_to_subscriber',
        'effective_date',
        'expiration_date',
        'priority',
        'coverage_percentage',
        'copay_amount',
        'deductible_amount',
        'deductible_remaining',
        'out_of_pocket_max',
        'out_of_pocket_remaining',
        'is_active',
        'coverage_details',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiration_date' => 'date',
        'coverage_percentage' => 'decimal:2',
        'copay_amount' => 'decimal:2',
        'deductible_amount' => 'decimal:2',
        'deductible_remaining' => 'decimal:2',
        'out_of_pocket_max' => 'decimal:2',
        'out_of_pocket_remaining' => 'decimal:2',
        'is_active' => 'boolean',
        'coverage_details' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function subscriberPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'subscriber_patient_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function invoicesAsPrimary(): HasMany
    {
        return $this->hasMany(Invoice::class, 'primary_insurance_id');
    }

    public function invoicesAsSecondary(): HasMany
    {
        return $this->hasMany(Invoice::class, 'secondary_insurance_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('priority', 'primary');
    }

    public function scopeSecondary($query)
    {
        return $query->where('priority', 'secondary');
    }

    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function isActive(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    public function getSubscriberNameAttribute($value): string
    {
        if ($this->subscriberPatient) {
            return $this->subscriberPatient->name;
        }

        return $value ?: 'Unknown Subscriber';
    }

    public function isSelfSubscribed(): bool
    {
        return $this->relationship_to_subscriber === 'self' ||
               ($this->subscriber_patient_id && $this->subscriber_patient_id === $this->patient_id);
    }

    public function getFhirCoverageResource(): array
    {
        return [
            'resourceType' => 'Coverage',
            'id' => "coverage-{$this->id}",
            'status' => $this->is_active ? 'active' : 'inactive',
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                        'code' => 'EHCPOL',
                        'display' => 'extended healthcare policy',
                    ],
                ],
            ],
            'subscriber' => $this->subscriberPatient ? [
                'reference' => "Patient/{$this->subscriberPatient->fhir_id}",
                'display' => $this->subscriberPatient->name,
            ] : null,
            'beneficiary' => [
                'reference' => "Patient/{$this->patient->fhir_id}",
                'display' => $this->patient->name,
            ],
            'relationship' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/subscriber-relationship',
                        'code' => $this->relationship_to_subscriber,
                        'display' => ucfirst($this->relationship_to_subscriber),
                    ],
                ],
            ],
            'period' => [
                'start' => $this->effective_date->format('Y-m-d'),
                'end' => $this->expiration_date?->format('Y-m-d'),
            ],
            'payor' => [
                [
                    'reference' => "Organization/insurance-{$this->insurance_company_id}",
                    'display' => $this->insuranceCompany->name ?? 'Unknown Insurance',
                ],
            ],
            'class' => [
                [
                    'type' => [
                        'coding' => [
                            [
                                'system' => 'http://terminology.hl7.org/CodeSystem/coverage-class',
                                'code' => 'policy',
                                'display' => 'Policy',
                            ],
                        ],
                    ],
                    'value' => $this->policy_number,
                ],
                $this->group_number ? [
                    'type' => [
                        'coding' => [
                            [
                                'system' => 'http://terminology.hl7.org/CodeSystem/coverage-class',
                                'code' => 'group',
                                'display' => 'Group',
                            ],
                        ],
                    ],
                    'value' => $this->group_number,
                ] : null,
            ],
            'costToBeneficiary' => [
                [
                    'type' => [
                        'coding' => [
                            [
                                'system' => 'http://terminology.hl7.org/CodeSystem/coverage-copay-type',
                                'code' => 'copay',
                                'display' => 'Copay',
                            ],
                        ],
                    ],
                    'valueMoney' => [
                        'value' => $this->copay_amount,
                        'currency' => 'USD',
                    ],
                ],
            ],
        ];
    }
}
