<?php

namespace App\Models\Finance;

use App\Models\BaseModel;
use App\Models\Treasury\TreasuryMovement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'paid' => 'boolean',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function treasuryMovement(): BelongsTo
    {
        return $this->belongsTo(TreasuryMovement::class);
    }

    // Métodos de negocio

    public function isPaid(): bool
    {
        return $this->paid === true;
    }

    public function isDue(): bool
    {
        return now()->isAfter($this->payment_date) && ! $this->isPaid();
    }

    public function markAsPaid(?TreasuryMovement $treasuryMovement = null): void
    {
        $this->update([
            'paid' => true,
            'paid_at' => now(),
            'treasury_movement_id' => $treasuryMovement?->id,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('paid', false);
    }

    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }

    public function scopeDue($query)
    {
        return $query->where('paid', false)->whereDate('payment_date', '<=', now());
    }
}
