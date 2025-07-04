<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceClaim extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_insurance_policy_id',
        'invoice_id',
        'encounter_id',
        'claim_number',
        'claim_date',
        'service_date',
        'billed_amount',
        'approved_amount',
        'paid_amount',
        'patient_responsibility',
        'copay_amount',
        'deductible_amount',
        'coinsurance_amount',
        'status',
        'rejection_reason',
        'rejection_details',
        'submitted_date',
        'processed_date',
        'payment_date',
        'authorization_number',
        'diagnosis_codes',
        'procedure_codes',
        'notes',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'service_date' => 'date',
        'submitted_date' => 'date',
        'processed_date' => 'date',
        'payment_date' => 'date',
        'billed_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'patient_responsibility' => 'decimal:2',
        'copay_amount' => 'decimal:2',
        'deductible_amount' => 'decimal:2',
        'coinsurance_amount' => 'decimal:2',
        'diagnosis_codes' => 'array',
        'procedure_codes' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope);
    }

    public function patientInsurancePolicy(): BelongsTo
    {
        return $this->belongsTo(PatientInsurancePolicy::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeDenied($query)
    {
        return $query->where('status', 'denied');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isDenied(): bool
    {
        return $this->status === 'denied';
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, ['submitted', 'processing']);
    }

    public function getRemainingBalance(): float
    {
        return $this->billed_amount - $this->paid_amount;
    }
}
