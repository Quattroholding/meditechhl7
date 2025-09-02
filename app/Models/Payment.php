<?php

namespace App\Models;

use App\Models\Scopes\PaymentScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'invoice_id',
        'patient_id',
        'client_id',
        'payment_number',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'transaction_id',
        'status',
        'notes',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'metadata' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'payment-'.Str::uuid();
            }
            if (empty($model->client_id)) {
                $model->client_id = auth()->user()?->getCurrentClient()?->id;
            }
            if (empty($model->payment_number)) {
                $model->payment_number = $model->generatePaymentNumber();
            }
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new PaymentScope);
    }

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Methods
    public function generatePaymentNumber(): string
    {
        $prefix = 'PAY-'.now()->format('Y-');
        $lastPayment = static::where('payment_number', 'like', $prefix.'%')
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$'.number_format($this->amount, 2);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Efectivo',
            'credit_card' => 'Tarjeta de Crédito',
            'debit_card' => 'Tarjeta de Débito',
            'bank_transfer' => 'Transferencia Bancaria',
            'check' => 'Cheque',
            'online' => 'Pago Online',
            'insurance' => 'Seguro',
            'other' => 'Otro',
            default => ucfirst(str_replace('_', ' ', $this->payment_method))
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'completed' => 'Completado',
            'failed' => 'Fallido',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
            default => ucfirst($this->status)
        };
    }
}
