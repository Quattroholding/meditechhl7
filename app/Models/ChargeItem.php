<?php

namespace App\Models;

use App\Enums\ChargeItemStatus;
use App\Models\Scopes\ChargeItemScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ScopedBy([ChargeItemScope::class])]
class ChargeItem extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'status',
        'code',
        'patient_id',
        'encounter_id',
        'appointment_id',
        'occurrence_date_time',
        'occurrence_period',
        'performer_practitioner_id',
        'performer_organization_id',
        'quantity',
        'unit_price_currency',
        'unit_price_value',
        'factor_override',
        'price_override',
        'identifier',
        'definition_uri',
        'definition_canonical',
        'part_of',
        'product_reference',
        'product_codeable_concept',
        'account',
        'note',
        'supporting_information',
        'cpt_code',
        'icd10_code',
        'revenue_code',
        'modifier_codes',
        'client_id',
        'service_catalog_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => ChargeItemStatus::class,
        'code' => 'array',
        'occurrence_date_time' => 'datetime',
        'occurrence_period' => 'array',
        'quantity' => 'decimal:2',
        'unit_price_value' => 'decimal:2',
        'factor_override' => 'decimal:4',
        'price_override' => 'decimal:2',
        'identifier' => 'array',
        'definition_canonical' => 'array',
        'part_of' => 'array',
        'product_reference' => 'array',
        'product_codeable_concept' => 'array',
        'account' => 'array',
        'supporting_information' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'charge-item-'.Str::uuid();
            }
            if (empty($model->client_id)) {
                $model->client_id = auth()->user()?->getCurrentClient()?->id;
            }
        });
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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function performerPractitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'performer_practitioner_id');
    }

    public function performerOrganization(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'performer_organization_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invoiceLineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBillable($query)
    {
        return $query->where('status', ChargeItemStatus::BILLABLE);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByEncounter($query, $encounterId)
    {
        return $query->where('encounter_id', $encounterId);
    }

    public function scopeByPractitioner($query, $practitionerId)
    {
        return $query->where('performer_practitioner_id', $practitionerId);
    }

    public function scopeByCptCode($query, $cptCode)
    {
        return $query->where('cpt_code', $cptCode);
    }

    public function scopeUnbilled($query)
    {
        return $query->whereDoesntHave('invoiceLineItems')
            ->where('status', ChargeItemStatus::BILLABLE);
    }

    // Accessors & Mutators
    public function getTotalPriceAttribute(): float
    {
        if ($this->price_override) {
            return (float) $this->price_override;
        }

        $basePrice = (float) $this->unit_price_value * (float) $this->quantity;

        if ($this->factor_override) {
            return $basePrice * (float) $this->factor_override;
        }

        return $basePrice;
    }

    public function getPrimaryCptCodeAttribute(): ?string
    {
        if ($this->cpt_code) {
            return $this->cpt_code;
        }

        // Extract from code array if available
        if (is_array($this->code) && isset($this->code['coding'])) {
            foreach ($this->code['coding'] as $coding) {
                if (isset($coding['system']) &&
                    (str_contains($coding['system'], 'cpt') || str_contains($coding['system'], 'hcpcs'))) {
                    return $coding['code'] ?? null;
                }
            }
        }

        return null;
    }

    public function getServiceDescriptionAttribute(): string
    {
        // Try to get description from code array
        if (is_array($this->code) && isset($this->code['text'])) {
            return $this->code['text'];
        }

        if (is_array($this->code) && isset($this->code['coding'])) {
            foreach ($this->code['coding'] as $coding) {
                if (isset($coding['display'])) {
                    return $coding['display'];
                }
            }
        }

        return $this->primary_cpt_code ?? 'Unknown Service';
    }

    // Methods
    public function markAsBilled(): bool
    {
        return $this->update(['status' => ChargeItemStatus::BILLED]);
    }

    public function markAsNotBillable(): bool
    {
        return $this->update(['status' => ChargeItemStatus::NOT_BILLABLE]);
    }

    public function canBeBilled(): bool
    {
        return $this->status === ChargeItemStatus::BILLABLE && ! $this->invoiceLineItems()->exists();
    }

    public function addToCoding(string $system, string $code, ?string $display = null): void
    {
        $coding = $this->code ?? ['coding' => []];

        if (! isset($coding['coding'])) {
            $coding['coding'] = [];
        }

        $coding['coding'][] = [
            'system' => $system,
            'code' => $code,
            'display' => $display,
        ];

        $this->update(['code' => $coding]);
    }

    public function toFhirResource(): array
    {
        return [
            'resourceType' => 'ChargeItem',
            'id' => $this->fhir_id,
            'status' => $this->status->value,
            'code' => $this->code,
            'subject' => [
                'reference' => "Patient/{$this->patient->fhir_id}",
            ],
            'context' => $this->encounter ? [
                'reference' => "Encounter/{$this->encounter->fhir_id}",
            ] : null,
            'occurrenceDateTime' => $this->occurrence_date_time?->toISOString(),
            'performer' => $this->performerPractitioner ? [[
                'actor' => [
                    'reference' => "Practitioner/{$this->performerPractitioner->fhir_id}",
                ],
            ]] : null,
            'quantity' => [
                'value' => $this->quantity,
            ],
            'priceOverride' => [
                'value' => $this->unit_price_value,
                'currency' => $this->unit_price_currency,
            ],
        ];
    }
}
