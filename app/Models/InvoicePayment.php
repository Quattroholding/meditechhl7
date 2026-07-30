<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'patient_id',
        'insurance_claim_id',
        'payment_reference',
        'amount',
        'payment_source',
        'payment_method',
        'payment_details',
        'payment_date',
        'posting_date',
        'transaction_id',
        'check_number',
        'authorization_code',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'posting_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function insuranceClaim(): BelongsTo
    {
        return $this->belongsTo(InsuranceClaim::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('payment_source', $source);
    }

    public function scopePatientPayments($query)
    {
        return $query->whereIn('payment_source', ['patient', 'copay', 'deductible', 'coinsurance']);
    }

    public function scopeInsurancePayments($query)
    {
        return $query->where('payment_source', 'insurance');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeCleared($query)
    {
        return $query->where('status', 'cleared');
    }

    public function isPatientPayment(): bool
    {
        return in_array($this->payment_source, ['patient', 'copay', 'deductible', 'coinsurance']);
    }

    public function isInsurancePayment(): bool
    {
        return $this->payment_source === 'insurance';
    }

    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    public function isCleared(): bool
    {
        return $this->status === 'cleared';
    }
}
