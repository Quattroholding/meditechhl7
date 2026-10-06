<?php

namespace App\Models\Finance;

use App\Models\Accounting\JournalEntry;
use App\Models\BaseModel;
use App\Models\Scopes\SupplierInvoiceScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierInvoice extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'client_id',
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'received_date',
        'due_date',
        'currency',
        'subtotal',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'balance',
        'cost_center_id',
        'notes',
        'document_path',
        'document_filename',
        'status',
        'approved_at',
        'approved_by',
        'journal_entry_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'received_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'approved_at' => 'datetime',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new SupplierInvoiceScope);
    }

    // Relaciones

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function costDistributions(): HasMany
    {
        return $this->hasMany(CostDistribution::class);
    }

    // Métodos de negocio

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPartiallyPaid(): bool
    {
        return $this->status === 'partial';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || (now()->isAfter($this->due_date) && ! $this->isPaid());
    }

    public function calculateBalance(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function markAsPaid(): void
    {
        $this->update([
            'paid_amount' => $this->total_amount,
            'balance' => 0,
            'status' => 'paid',
        ]);
    }

    public function recordPayment(float $amount): void
    {
        $this->paid_amount += $amount;
        $this->balance = $this->calculateBalance();
        $this->status = $this->balance <= 0 ? 'paid' : 'partial';
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }
}
