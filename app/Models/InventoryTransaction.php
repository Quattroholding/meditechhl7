<?php

namespace App\Models;

use App\Enums\InventoryTransactionType;
use App\Models\Scopes\InventoryTransactionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    // No soft deletes - transactions are immutable

    protected $fillable = [
        'transaction_type', 'transaction_date', 'inventory_item_id', 'quantity_change',
        'unit_of_measure', 'quantity_before', 'quantity_after', 'from_location_client_id',
        'from_location_branch_id', 'from_location_practitioner_id', 'from_location_consulting_room_id',
        'to_location_client_id', 'to_location_branch_id', 'to_location_practitioner_id',
        'to_location_consulting_room_id', 'lot_number', 'serial_number', 'expiration_date',
        'unit_cost', 'total_cost', 'supply_request_id', 'supply_delivery_id', 'charge_item_id',
        'patient_id', 'encounter_id', 'performed_by_user_id', 'authorized_by_user_id',
        'reason', 'notes', 'client_id',
    ];

    protected function casts(): array
    {
        return [
            'transaction_type' => InventoryTransactionType::class,
            'transaction_date' => 'datetime',
            'quantity_change' => 'decimal:2',
            'quantity_before' => 'decimal:2',
            'quantity_after' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'expiration_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new InventoryTransactionScope);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function supplyRequest(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    public function supplyDelivery(): BelongsTo
    {
        return $this->belongsTo(SupplyDelivery::class);
    }

    public function chargeItem(): BelongsTo
    {
        return $this->belongsTo(ChargeItem::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function performedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    public function authorizedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by_user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Location relationships
    public function fromClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'from_location_client_id');
    }

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_location_branch_id');
    }

    public function fromPractitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'from_location_practitioner_id');
    }

    public function fromConsultingRoom(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class, 'from_location_consulting_room_id');
    }

    public function toClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'to_location_client_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_location_branch_id');
    }

    public function toPractitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'to_location_practitioner_id');
    }

    public function toConsultingRoom(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class, 'to_location_consulting_room_id');
    }
}
