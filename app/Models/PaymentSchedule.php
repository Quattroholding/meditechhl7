<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentSchedule extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'supplier_invoice_id',
        'payment_date',
        'amount',
        'paid',
        'paid_at',
        'treasury_movement_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    // ===== Relationships =====

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function treasuryMovement(): BelongsTo
    {
        return $this->belongsTo(TreasuryMovement::class);
    }

    // ===== Methods =====

    public function markAsPaid($treasuryMovementId = null): void
    {
        $this->update([
            'paid' => true,
            'paid_at' => now(),
            'treasury_movement_id' => $treasuryMovementId,
        ]);

        // Actualizar balance de factura
        $this->supplierInvoice->updateBalance();
        $this->supplierInvoice->updateStatus();
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function isPending(): bool
    {
        return ! $this->paid && $this->payment_date > now()->toDateString();
    }

    public function isOverdue(): bool
    {
        return ! $this->paid && $this->payment_date < now()->toDateString();
    }

    public function daysUntilPayment(): int
    {
        return now()->diffInDays($this->payment_date);
    }
}
