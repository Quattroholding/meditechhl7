<?php

namespace App\Models;

use App\Models\Scopes\InvoiceScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ScopedBy([InvoiceScope::class])]
class Invoice extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'status',
        'identifier',
        'invoice_number',
        'patient_id',
        'encounter_id',
        'account_id',
        'issuer_organization_id',
        'recipient_organization_id',
        'recipient_patient_id',
        'performer_practitioner_id',
        'date',
        'issue_date',
        'due_date',
        'payment_terms',
        'period_start_end',
        'currency',
        'subtotal_amount',
        'tax_amount',
        'total_amount',
        'total_preTax',
        'total_tax',
        'total_gross',
        'total_net',
        'payment_status',
        'amount_paid',
        'amount_due',
        'payment_date',
        'payment_method',
        'payment_reference',
        'type',
        'participant',
        'cancellation_reason',
        'note',
        'description',
        'billing_address',
        'shipping_address',
        'reference_number',
        'terms_and_conditions',
        'client_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'identifier' => 'array',
        'date' => 'date',
        'issue_date' => 'date',
        'due_date' => 'date',
        'period_start_end' => 'array',
        'subtotal_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_preTax' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_gross' => 'decimal:2',
        'total_net' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'payment_date' => 'date',
        'participant' => 'array',
        'billing_address' => 'array',
        'shipping_address' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'invoice-'.Str::uuid();
            }
            if (empty($model->client_id)) {
                $model->client_id = auth()->user()?->getCurrentClient()?->id;
            }
            if (empty($model->invoice_number)) {
                $model->invoice_number = $model->generateInvoiceNumber();
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

    public function performerPractitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'performer_practitioner_id');
    }

    public function issuerOrganization(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'issuer_organization_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class)->orderBy('sequence');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date', 'desc');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('payment_status', ['pending', 'partial']);
    }

    public function scopeByEncounter($query, $encounterId)
    {
        return $query->where('encounter_id', $encounterId);
    }

    // Accessors
    public function getBalanceAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->amount_paid;
    }

    // Methods
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->format('Y-');
        $lastInvoice = static::withoutGlobalScope(InvoiceScope::class)->where('invoice_number', 'like', $prefix.'%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function addPayment(float $amount, ?string $method = null): bool
    {
        $newAmountPaid = (float) $this->amount_paid + $amount;

        if ($newAmountPaid > $this->total_net) {
            return false;
        }

        $this->amount_paid = $newAmountPaid;
        $this->amount_due = $this->total_net - $this->amount_paid;

        if ($this->amount_due <= 0.01) {
            $this->payment_status = 'paid';
            $this->payment_date = now();
        } else {
            $this->payment_status = 'partial';
        }

        if ($method) {
            $this->payment_method = $method;
        }

        return $this->save();
    }

    public function addLineItemFromChargeItem(ChargeItem $chargeItem): InvoiceLineItem
    {
        $sequence = $this->lineItems()->max('sequence') + 1;

        return $this->lineItems()->create([
            'fhir_id' => 'line-'.Str::uuid(),
            'sequence' => $sequence,
            'charge_item_id' => $chargeItem->id,
            'service_code' => $chargeItem->code,
            'service_description' => $chargeItem->service_description,
            'quantity' => $chargeItem->quantity,
            'unit_price' => $chargeItem->unit_price_value,
            'line_total_pretax' => $chargeItem->total_price,
            'line_total_gross' => $chargeItem->total_price,
            'cpt_code' => $chargeItem->primary_cpt_code,
            'performer_practitioner_id' => $chargeItem->performer_practitioner_id,
            'client_id' => $this->client_id,
        ]);
    }
}
