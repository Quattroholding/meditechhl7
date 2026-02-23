<?php

namespace App\Models;

use App\Enums\ChargeItemStatus;
use App\Models\Scopes\ServiceCatalogScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceCatalog extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'service_catalog';

    protected $fillable = [
        'fhir_id',
        'code',
        'cpt_code',
        'hcpcs_code',
        'icd10_pcs_code',
        'snomed_code',
        'name',
        'description',
        'service_type',
        'base_price',
        'currency',
        'billing_unit',
        'duration_minutes',
        'specialty',
        'body_system',
        'complexity',
        'requires_authorization',
        'revenue_code',
        'cost_center',
        'gl_account',
        'cost_estimate',
        'markup_percentage',
        'is_active',
        'effective_date',
        'expiration_date',
        'available_locations',
        'qualified_practitioners',
        'covered_by_insurance',
        'insurance_codes',
        'patient_copay',
        'insurance_allowable',
        'coding_systems',
        'related_services',
        'clinical_notes',
        'prerequisites',
        'client_id',
        'practitioner_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'duration_minutes' => 'decimal:2',
        'requires_authorization' => 'boolean',
        'cost_estimate' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'expiration_date' => 'date',
        'available_locations' => 'array',
        'qualified_practitioners' => 'array',
        'covered_by_insurance' => 'boolean',
        'insurance_codes' => 'array',
        'patient_copay' => 'decimal:2',
        'insurance_allowable' => 'decimal:2',
        'coding_systems' => 'array',
        'related_services' => 'array',
        'prerequisites' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ServiceCatalogScope);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'service-'.Str::uuid();
            }
            if (empty($model->client_id)) {
                $model->client_id = auth()->user()?->getCurrentClient()?->id;
            }
            if (empty($model->code)) {
                $model->code = $model->generateServiceCode();
            }
        });
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function chargeItems(): HasMany
    {
        return $this->hasMany(ChargeItem::class, 'cpt_code', 'cpt_code');
    }

    public function cptCodeInfo(): BelongsTo
    {
        return $this->belongsTo(CptCode::class, 'cpt_code', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('effective_date')
                    ->orWhere('effective_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expiration_date')
                    ->orWhere('expiration_date', '>=', now());
            });
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeBySpecialty($query, $specialty)
    {
        return $query->where('specialty', $specialty);
    }

    public function scopeByCptCode($query, $cptCode)
    {
        return $query->where('cpt_code', $cptCode);
    }

    public function scopeByComplexity($query, $complexity)
    {
        return $query->where('complexity', $complexity);
    }

    public function scopeCoveredByInsurance($query)
    {
        return $query->where('covered_by_insurance', true);
    }

    public function scopeRequiresAuthorization($query)
    {
        return $query->where('requires_authorization', true);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('base_price', [$minPrice, $maxPrice]);
    }

    public function scopeForPractitioner($query, $practitionerId)
    {
        return $query->where(function ($q) use ($practitionerId) {
            $q->whereNull('qualified_practitioners')
                ->orWhereJsonContains('qualified_practitioners', $practitionerId);
        });
    }

    public function scopeForLocation($query, $locationId)
    {
        return $query->where(function ($q) use ($locationId) {
            $q->whereNull('available_locations')
                ->orWhereJsonContains('available_locations', $locationId);
        });
    }

    // Accessors & Mutators
    public function getEffectivePriceAttribute(): float
    {
        $basePrice = (float) $this->base_price;

        if ($this->markup_percentage > 0) {
            return $basePrice * (1 + ((float) $this->markup_percentage / 100));
        }

        return $basePrice;
    }

    public function getProfitMarginAttribute(): float
    {
        if (! $this->cost_estimate || $this->cost_estimate <= 0) {
            return 0.0;
        }

        return $this->effective_price - (float) $this->cost_estimate;
    }

    public function getProfitMarginPercentageAttribute(): float
    {
        if (! $this->cost_estimate || $this->cost_estimate <= 0) {
            return 0.0;
        }

        return (($this->effective_price - (float) $this->cost_estimate) / (float) $this->cost_estimate) * 100;
    }

    public function getEstimatedDurationHoursAttribute(): float
    {
        return $this->duration_minutes ? (float) $this->duration_minutes / 60 : 0.0;
    }

    public function getIsCurrentlyAvailableAttribute(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->effective_date && $this->effective_date->isAfter($now)) {
            return false;
        }

        if ($this->expiration_date && $this->expiration_date->isBefore($now)) {
            return false;
        }

        return true;
    }

    // Methods
    public static function generateServiceCodeWithOffset(string $serviceType, int $offset = 0): string
    {
        $prefix = strtoupper(substr($serviceType ?? 'SVC', 0, 3));

        // Buscar el último número usado para este prefijo GLOBALMENTE (sin scope global y sin filtro de cliente)
        // porque el constraint único es global, no por cliente
        $lastCode = static::withoutGlobalScope(ServiceCatalogScope::class)
            ->where('code', 'like', $prefix.'_%')
            ->orderByRaw('CAST(SUBSTRING(code, '.(strlen($prefix) + 2).') AS UNSIGNED) DESC')
            ->value('code');

        if ($lastCode) {
            // Extraer el número del último código y sumar 1
            preg_match('/'.$prefix.'_(\d+)/', $lastCode, $matches);
            $number = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
        } else {
            $number = 1;
        }

        // Add offset for retry logic
        $number += $offset;

        // Verificar que el código no exista GLOBALMENTE (por si acaso)
        // Intentar hasta 100 veces para encontrar un código único
        $maxAttempts = 100;
        $attempts = 0;

        do {
            $code = $prefix.'_'.str_pad($number, 4, '0', STR_PAD_LEFT);
            $exists = static::withoutGlobalScope(ServiceCatalogScope::class)
                ->where('code', $code)
                ->exists();
            if ($exists) {
                $number++;
                $attempts++;
            }

            if ($attempts >= $maxAttempts) {
                // Fallback: add random suffix if we can't find a unique code
                $code = $prefix.'_'.str_pad($number, 4, '0', STR_PAD_LEFT).'_'.substr(md5(uniqid()), 0, 4);
                break;
            }
        } while ($exists);

        return $code;
    }

    public function generateServiceCode(): string
    {
        $prefix = strtoupper(substr($this->service_type ?? 'SVC', 0, 3));

        // Buscar el último número usado para este prefijo GLOBALMENTE (sin scope global y sin filtro de cliente)
        // porque el constraint único es global, no por cliente
        $lastCode = static::withoutGlobalScope(ServiceCatalogScope::class)
            ->where('code', 'like', $prefix.'_%')
            ->orderByRaw('CAST(SUBSTRING(code, '.(strlen($prefix) + 2).') AS UNSIGNED) DESC')
            ->value('code');

        if ($lastCode) {
            // Extraer el número del último código y sumar 1
            preg_match('/'.$prefix.'_(\d+)/', $lastCode, $matches);
            $number = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
        } else {
            $number = 1;
        }

        // Verificar que el código no exista GLOBALMENTE (por si acaso)
        // Intentar hasta 100 veces para encontrar un código único
        $maxAttempts = 100;
        $attempts = 0;

        do {
            $code = $prefix.'_'.str_pad($number, 4, '0', STR_PAD_LEFT);
            $exists = static::withoutGlobalScope(ServiceCatalogScope::class)
                ->where('code', $code)
                ->exists();
            if ($exists) {
                $number++;
                $attempts++;
            }

            if ($attempts >= $maxAttempts) {
                // Fallback: add random suffix if we can't find a unique code
                $code = $prefix.'_'.str_pad($number, 4, '0', STR_PAD_LEFT).'_'.substr(md5(uniqid()), 0, 4);
                break;
            }
        } while ($exists);

        return $code;
    }

    public function createChargeItem(
        int $patientId,
        int $practitionerId,
        ?int $encounterId = null,
        ?int $appointmentId = null,
        float $quantity = 1.0,
        ?float $customPrice = null
    ): ChargeItem {
        $unitPrice = $customPrice ?? $this->effective_price;

        // Si cpt_code no es null, usar code + description_es de la tabla cpt_codes
        // Si es null, usar el campo name de service_catalog
        $displayName = $this->cpt_code && $this->cptCodeInfo
            ? $this->cptCodeInfo->code.' | '.$this->cptCodeInfo->description_es
            : $this->name;

        return ChargeItem::create([
            'fhir_id' => 'charge-'.Str::uuid(),
            'status' => ChargeItemStatus::BILLABLE,
            'service_catalog_id' => $this->id,
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://www.ama-assn.org/go/cpt',
                        'code' => $this->cpt_code,
                        'display' => $displayName,
                    ],
                ],
                'text' => $displayName,
            ],
            'patient_id' => $patientId,
            'encounter_id' => $encounterId,
            'appointment_id' => $appointmentId,
            'performer_practitioner_id' => $practitionerId,
            'performer_organization_id' => $this->client_id,
            'occurrence_date_time' => now(),
            'quantity' => $quantity,
            'unit_price_value' => $unitPrice,
            'cpt_code' => $this->cpt_code,
            'revenue_code' => $this->revenue_code,
            'client_id' => $this->client_id,
        ]);
    }

    public function calculatePriceForInsurance(?string $insuranceType = null): float
    {
        if (! $this->covered_by_insurance) {
            return $this->effective_price;
        }

        // If insurance allowable is set, use that
        if ($this->insurance_allowable > 0) {
            return (float) $this->insurance_allowable;
        }

        // Check insurance-specific codes
        if ($insuranceType && $this->insurance_codes) {
            $insuranceCodes = $this->insurance_codes;
            if (isset($insuranceCodes[$insuranceType]['allowable'])) {
                return (float) $insuranceCodes[$insuranceType]['allowable'];
            }
        }

        return $this->effective_price;
    }

    public function getPatientResponsibility(?string $insuranceType = null): float
    {
        if (! $this->covered_by_insurance) {
            return $this->effective_price;
        }

        $insurancePrice = $this->calculatePriceForInsurance($insuranceType);
        $copay = (float) $this->patient_copay;

        return min($copay, $insurancePrice);
    }

    public function isAvailableForPractitioner(int $practitionerId): bool
    {
        if (! $this->is_currently_available) {
            return false;
        }

        if (! $this->qualified_practitioners) {
            return true; // Available to all practitioners
        }

        return in_array($practitionerId, $this->qualified_practitioners);
    }

    public function isAvailableAtLocation(int $locationId): bool
    {
        if (! $this->is_currently_available) {
            return false;
        }

        if (! $this->available_locations) {
            return true; // Available at all locations
        }

        return in_array($locationId, $this->available_locations);
    }

    public function addQualifiedPractitioner(int $practitionerId): void
    {
        $practitioners = $this->qualified_practitioners ?? [];

        if (! in_array($practitionerId, $practitioners)) {
            $practitioners[] = $practitionerId;
            $this->qualified_practitioners = $practitioners;
            $this->save();
        }
    }

    public function removeQualifiedPractitioner(int $practitionerId): void
    {
        $practitioners = $this->qualified_practitioners ?? [];

        $key = array_search($practitionerId, $practitioners);
        if ($key !== false) {
            unset($practitioners[$key]);
            $this->qualified_practitioners = array_values($practitioners);
            $this->save();
        }
    }

    public function addAvailableLocation(int $locationId): void
    {
        $locations = $this->available_locations ?? [];

        if (! in_array($locationId, $locations)) {
            $locations[] = $locationId;
            $this->available_locations = $locations;
            $this->save();
        }
    }

    public function deactivate(?string $reason = null): bool
    {
        $this->is_active = false;
        $this->expiration_date = now();

        if ($reason) {
            $this->clinical_notes = ($this->clinical_notes ? $this->clinical_notes."\n\n" : '')
                                  .'Deactivated: '.$reason.' ('.now()->toDateString().')';
        }

        return $this->save();
    }

    public function toFhirResource(): array
    {
        return [
            'resourceType' => 'HealthcareService',
            'id' => $this->fhir_id,
            'identifier' => [
                [
                    'value' => $this->code,
                ],
            ],
            'active' => $this->is_currently_available,
            'type' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://www.ama-assn.org/go/cpt',
                            'code' => $this->cpt_code,
                            'display' => $this->name,
                        ],
                    ],
                ],
            ],
            'name' => $this->name,
            'comment' => $this->description,
            'category' => [
                [
                    'coding' => [
                        [
                            'code' => $this->service_type,
                            'display' => ucfirst(str_replace('_', ' ', $this->service_type)),
                        ],
                    ],
                ],
            ],
            'providedBy' => [
                'reference' => "Organization/{$this->client->fhir_id}",
            ],
        ];
    }
}
