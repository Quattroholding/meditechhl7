<?php

namespace App\Models;

use App\Enums\SupplyReturnReason;
use App\Enums\UnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyReturn extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supply_delivery_id',
        'supply_request_id',
        'inventory_item_id',
        'patient_id',
        'encounter_id',
        'quantity_returned',
        'quantity_originally_dispensed',
        'unit_of_measure',
        'reason',
        'notes',
        'lot_number',
        'serial_number',
        'expiration_date',
        'returned_to_branch_id',
        'returned_to_practitioner_id',
        'returned_by_user_id',
        'returned_at',
        'charge_item_id',
        'amount_reversed',
    ];

    protected function casts(): array
    {
        return [
            'quantity_returned' => 'decimal:2',
            'quantity_originally_dispensed' => 'decimal:2',
            'amount_reversed' => 'decimal:2',
            'reason' => SupplyReturnReason::class,
            'unit_of_measure' => UnitOfMeasure::class,
            'expiration_date' => 'date',
            'returned_at' => 'datetime',
        ];
    }

    // Relaciones
    public function supplyDelivery(): BelongsTo
    {
        return $this->belongsTo(SupplyDelivery::class);
    }

    public function supplyRequest(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function returnedToBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'returned_to_branch_id');
    }

    public function returnedToPractitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'returned_to_practitioner_id');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by_user_id');
    }

    public function chargeItem(): BelongsTo
    {
        return $this->belongsTo(ChargeItem::class);
    }

    /**
     * Check if this is a full return (all quantity returned)
     */
    public function isFullReturn(): bool
    {
        return $this->quantity_returned == $this->quantity_originally_dispensed;
    }

    /**
     * Check if this is a partial return
     */
    public function isPartialReturn(): bool
    {
        return $this->quantity_returned < $this->quantity_originally_dispensed;
    }
}
