<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InvoiceLineItem extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'invoice_id',
        'sequence',
        'sequence_number',
        'charge_item_id',
        'service_code',
        'service_description',
        'service_date',
        'service_period',
        'quantity',
        'unit_of_measure',
        'unit_price',
        'line_total',
        'currency',
        'discount_percentage',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'line_total_pretax',
        'line_total_tax',
        'line_total_gross',
        'modifier',
        'revenue_category',
        'product_or_service_end',
        'cpt_code',
        'hcpcs_code',
        'icd10_code',
        'revenue_code',
        'modifier_codes',
        'performer_practitioner_id',
        'performing_organization_id',
        'note',
        'supporting_information',
        'client_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'service_code' => 'array',
        'service_date' => 'date',
        'service_period' => 'array',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'line_total_pretax' => 'decimal:2',
        'line_total_tax' => 'decimal:2',
        'line_total_gross' => 'decimal:2',
        'modifier' => 'array',
        'revenue_category' => 'array',
        'product_or_service_end' => 'array',
        'modifier_codes' => 'array',
        'supporting_information' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'line-item-'.Str::uuid();
            }
            if (empty($model->client_id)) {
                $model->client_id = auth()->user()?->getCurrentClient()?->id;
            }
        });

        static::saving(function ($model) {
            $model->calculateLineTotals();
        });
    }

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function chargeItem(): BelongsTo
    {
        return $this->belongsTo(ChargeItem::class);
    }

    public function performerPractitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'performer_practitioner_id');
    }

    public function performingOrganization(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'performing_organization_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Scopes
    public function scopeByInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    public function scopeByChargeItem($query, $chargeItemId)
    {
        return $query->where('charge_item_id', $chargeItemId);
    }

    public function scopeByPractitioner($query, $practitionerId)
    {
        return $query->where('performer_practitioner_id', $practitionerId);
    }

    public function scopeByCptCode($query, $cptCode)
    {
        return $query->where('cpt_code', $cptCode);
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->whereHas('chargeItem', function ($q) use ($serviceType) {
            $q->whereJsonContains('code->coding', ['system' => $serviceType]);
        });
    }

    // Accessors & Mutators
    public function getSubtotalAttribute(): float
    {
        return (float) $this->quantity * (float) $this->unit_price;
    }

    public function getDiscountAmountCalculatedAttribute(): float
    {
        if ($this->discount_amount > 0) {
            return (float) $this->discount_amount;
        }

        if ($this->discount_percentage > 0) {
            return $this->subtotal * ((float) $this->discount_percentage / 100);
        }

        return 0.0;
    }

    public function getTaxAmountCalculatedAttribute(): float
    {
        if ($this->tax_amount > 0) {
            return (float) $this->tax_amount;
        }

        $taxableAmount = $this->subtotal - $this->discount_amount_calculated;

        return $taxableAmount * ((float) $this->tax_rate);
    }

    public function getPrimaryCptCodeAttribute(): ?string
    {
        if ($this->cpt_code) {
            return $this->cpt_code;
        }

        // Extract from service_code array if available
        if (is_array($this->service_code) && isset($this->service_code['coding'])) {
            foreach ($this->service_code['coding'] as $coding) {
                if (isset($coding['system']) &&
                    (str_contains($coding['system'], 'cpt') || str_contains($coding['system'], 'hcpcs'))) {
                    return $coding['code'] ?? null;
                }
            }
        }

        return null;
    }

    public function getFormattedServiceDescriptionAttribute(): string
    {
        $description = $this->service_description;

        if ($this->primary_cpt_code) {
            $description .= " (CPT: {$this->primary_cpt_code})";
        }

        if ($this->quantity != 1) {
            $description .= " x{$this->quantity}";
        }

        return $description;
    }

    // Methods
    public function calculateLineTotals(): void
    {
        // Calculate subtotal
        $subtotal = $this->subtotal;

        // Apply discount
        $discountAmount = $this->discount_amount_calculated;
        $preTaxAmount = $subtotal - $discountAmount;

        // Calculate tax
        $taxAmount = $this->tax_amount_calculated;
        $grossAmount = $preTaxAmount + $taxAmount;

        // Update calculated fields
        $this->line_total_pretax = $preTaxAmount;
        $this->line_total_tax = $taxAmount;
        $this->line_total_gross = $grossAmount;

        // Update discount amount if calculated from percentage
        if ($this->discount_percentage > 0 && $this->discount_amount <= 0) {
            $this->discount_amount = $discountAmount;
        }

        // Update tax amount if calculated from rate
        if ($this->tax_rate > 0 && $this->tax_amount <= 0) {
            $this->tax_amount = $taxAmount;
        }
    }

    public function applyDiscount(float $percentage): void
    {
        $this->discount_percentage = $percentage;
        $this->discount_amount = 0; // Reset fixed amount
        $this->calculateLineTotals();
    }

    public function applyFixedDiscount(float $amount): void
    {
        $this->discount_amount = $amount;
        $this->discount_percentage = 0; // Reset percentage
        $this->calculateLineTotals();
    }

    public function applyTax(float $rate): void
    {
        $this->tax_rate = $rate;
        $this->tax_amount = 0; // Reset fixed amount
        $this->calculateLineTotals();
    }

    public function applyFixedTax(float $amount): void
    {
        $this->tax_amount = $amount;
        $this->tax_rate = 0; // Reset rate
        $this->calculateLineTotals();
    }

    public function updateQuantity(float $newQuantity): void
    {
        $this->quantity = $newQuantity;
        $this->calculateLineTotals();
        $this->save();
    }

    public function updateUnitPrice(float $newPrice): void
    {
        $this->unit_price = $newPrice;
        $this->calculateLineTotals();
        $this->save();
    }

    public function addModifier(string $modifierCode, ?string $description = null): void
    {
        $modifiers = $this->modifier_codes ?? [];

        if (! in_array($modifierCode, $modifiers)) {
            $modifiers[] = $modifierCode;
            $this->modifier_codes = $modifiers;
        }

        // Also add to FHIR modifier array
        $fhirModifiers = $this->modifier ?? ['coding' => []];
        if (! isset($fhirModifiers['coding'])) {
            $fhirModifiers['coding'] = [];
        }

        $fhirModifiers['coding'][] = [
            'system' => 'http://www.ama-assn.org/go/cpt',
            'code' => $modifierCode,
            'display' => $description,
        ];

        $this->modifier = $fhirModifiers;
        $this->save();
    }

    public function getEffectivePrice(): float
    {
        return $this->line_total_gross / $this->quantity;
    }

    public function toFhirLineItem(): array
    {
        return [
            'sequence' => $this->sequence,
            'chargeItemReference' => $this->chargeItem ? [
                'reference' => "ChargeItem/{$this->chargeItem->fhir_id}",
            ] : null,
            'priceComponent' => [
                [
                    'type' => 'base',
                    'amount' => [
                        'value' => $this->unit_price,
                        'currency' => $this->invoice->currency ?? 'USD',
                    ],
                ],
            ],
            'quantity' => [
                'value' => $this->quantity,
                'unit' => $this->unit_of_measure ?? 'each',
            ],
            'totalPriceComponent' => [
                [
                    'type' => 'base',
                    'amount' => [
                        'value' => $this->line_total_pretax,
                        'currency' => $this->invoice->currency ?? 'USD',
                    ],
                ],
                [
                    'type' => 'tax',
                    'amount' => [
                        'value' => $this->line_total_tax,
                        'currency' => $this->invoice->currency ?? 'USD',
                    ],
                ],
            ],
        ];
    }
}
