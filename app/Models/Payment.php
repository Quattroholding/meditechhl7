<?php

namespace App\Models;

use App\Models\Accounting\JournalEntry;
use App\Models\Scopes\PaymentScope;
use App\Models\Treasury\TreasuryMovement;
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
        // Accounting fields
        'journal_entry_id',
        'treasury_movement_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'metadata' => 'array',
        // Accounting casts
        'journal_entry_id' => 'integer',
        'treasury_movement_id' => 'integer',
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

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function treasuryMovement(): BelongsTo
    {
        return $this->belongsTo(TreasuryMovement::class);
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

    public function scopeWithAccountingEntry($query)
    {
        return $query->whereNotNull('journal_entry_id');
    }

    public function scopeWithTreasuryMovement($query)
    {
        return $query->whereNotNull('treasury_movement_id');
    }

    // Methods
    public function generatePaymentNumber(): string
    {
        $prefix = 'PAY-'.now()->format('Y-');

        // Use withoutGlobalScopes to ensure we check ALL payments across all clients
        // Use lockForUpdate to prevent race conditions when generating sequential numbers
        $lastPayment = static::withoutGlobalScopes()
            ->where('payment_number', 'like', $prefix.'%')
            ->orderBy('payment_number', 'desc')
            ->lockForUpdate()
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

    // Financial methods
    public function hasAccountingEntry(): bool
    {
        return $this->journal_entry_id !== null;
    }

    public function hasTreasuryMovement(): bool
    {
        return $this->treasury_movement_id !== null;
    }

    public function getSource(): string
    {
        return match ($this->payment_method) {
            'cash' => 'cash',
            'credit_card' => 'card',
            'debit_card' => 'card',
            'bank_transfer' => 'bank',
            'check' => 'check',
            'online' => 'online',
            'insurance' => 'insurance',
            default => 'other',
        };
    }

    public function generateAccountingEntry(): JournalEntry
    {
        if ($this->hasAccountingEntry()) {
            return $this->journalEntry;
        }

        $journalEntry = JournalEntry::create([
            'uuid' => Str::uuid(),
            'client_id' => $this->client_id,
            'entry_number' => 'PAY-'.$this->payment_number,
            'entry_date' => $this->payment_date ?? now()->toDateString(),
            'document_type' => 'payment',
            'document_number' => $this->payment_number,
            'description' => 'Payment #'.$this->payment_number.' - Invoice: '.$this->invoice?->invoice_number,
            'status' => 'draft',
            'source_type' => Payment::class,
            'source_id' => $this->id,
        ]);

        $this->update(['journal_entry_id' => $journalEntry->id]);

        return $journalEntry;
    }

    public function generateTreasuryMovement(): TreasuryMovement
    {
        if ($this->hasTreasuryMovement()) {
            return $this->treasuryMovement;
        }

        $treasuryMovement = TreasuryMovement::create([
            'client_id' => $this->client_id,
            'movement_type' => 'deposit',
            'source_type' => Payment::class,
            'source_id' => $this->id,
            'amount' => $this->amount,
            'movement_date' => $this->payment_date ?? now(),
            'payment_method' => $this->payment_method,
            'reference_number' => $this->reference_number,
            'description' => 'Payment #'.$this->payment_number,
            'status' => 'pending',
        ]);

        $this->update(['treasury_movement_id' => $treasuryMovement->id]);

        return $treasuryMovement;
    }

    public function syncTreasuryMovement(): void
    {
        if ($this->hasTreasuryMovement()) {
            // Update existing movement
            $this->treasuryMovement->update([
                'amount' => $this->amount,
                'description' => 'Payment #'.$this->payment_number,
            ]);
        } else {
            // Create new movement
            $this->generateTreasuryMovement();
        }
    }
}
