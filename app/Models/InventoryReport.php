<?php

namespace App\Models;

use App\Models\Scopes\InventoryReportScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InventoryReport extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id', 'inventory_item_id', 'status', 'client_id', 'branch_id',
        'practitioner_id', 'consulting_room_id', 'quantity_on_hand',
        'quantity_reserved', 'lot_number', 'serial_number', 'expiration_date',
        'reported_datetime', 'reported_by_practitioner_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'decimal:2',
            'quantity_reserved' => 'decimal:2',
            'expiration_date' => 'date',
            'reported_datetime' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new InventoryReportScope);

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'inventory-report-'.Str::uuid();
            }
        });
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function consultingRoom(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class);
    }

    public function reportedByPractitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'reported_by_practitioner_id');
    }

    /**
     * Get quantity available (on hand minus reserved)
     */
    public function getQuantityAvailableAttribute(): float
    {
        return (float) ($this->quantity_on_hand - $this->quantity_reserved);
    }

    /**
     * Get inventory report for a practitioner, respecting their inventory configuration
     */
    public static function getForPractitioner(
        int $inventoryItemId,
        Practitioner $practitioner,
        ?int $branchId = null
    ): ?self {
        if ($practitioner->has_individual_inventory) {
            // Individual inventory for this practitioner
            return self::where('inventory_item_id', $inventoryItemId)
                ->where('practitioner_id', $practitioner->id)
                ->where('status', 'active')
                ->first();
        } else {
            // Shared branch inventory
            return self::where('inventory_item_id', $inventoryItemId)
                ->where('branch_id', $branchId)
                ->whereNull('practitioner_id')
                ->where('status', 'active')
                ->first();
        }
    }

    /**
     * Export as FHIR InventoryReport resource
     */
    public function toFhirResource(): array
    {
        return [
            'resourceType' => 'InventoryReport',
            'id' => $this->fhir_id,
            'status' => $this->status,
            'countType' => 'snapshot',
            'operationType' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/inventoryreport-operation',
                        'code' => 'snapshot',
                    ],
                ],
            ],
            'reportedDateTime' => $this->reported_datetime?->toIso8601String(),
            'inventoryListing' => [
                [
                    'item' => [
                        'reference' => "InventoryItem/{$this->inventoryItem->fhir_id}",
                    ],
                    'countingDateTime' => $this->updated_at->toIso8601String(),
                    'quantity' => [
                        'value' => (float) $this->quantity_on_hand,
                    ],
                ],
            ],
        ];
    }
}
