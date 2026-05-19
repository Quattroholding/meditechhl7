<?php

namespace App\Models;

use App\Enums\InventoryItemStatus;
use App\Enums\InventoryItemType;
use App\Enums\UnitOfMeasure;
use App\Models\Scopes\InventoryItemScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InventoryItem extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id', 'status', 'category', 'code', 'name', 'description',
        'sku', 'barcode', 'item_type', 'base_cost', 'base_price', 'currency',
        'unit_of_measure', 'requires_prescription', 'track_by_lot',
        'track_by_serial', 'expiration_tracking', 'reorder_point',
        'reorder_quantity', 'characteristic', 'client_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => InventoryItemStatus::class,
            'item_type' => InventoryItemType::class,
            'unit_of_measure' => UnitOfMeasure::class,
            'category' => 'array',
            'code' => 'array',
            'characteristic' => 'array',
            'base_cost' => 'decimal:2',
            'base_price' => 'decimal:2',
            'requires_prescription' => 'boolean',
            'track_by_lot' => 'boolean',
            'track_by_serial' => 'boolean',
            'expiration_tracking' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new InventoryItemScope);

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'inventory-item-'.Str::uuid();
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function inventoryReports(): HasMany
    {
        return $this->hasMany(InventoryReport::class);
    }

    public function supplyRequests(): HasMany
    {
        return $this->hasMany(SupplyRequest::class);
    }

    public function supplyDeliveries(): HasMany
    {
        return $this->hasMany(SupplyDelivery::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Get stock level for specific location
     */
    public function getStockLevel(?int $branchId = null, ?int $practitionerId = null): float
    {
        $query = $this->inventoryReports()
            ->where('status', 'active');

        if ($practitionerId) {
            // Get stock from specific practitioner's inventory
            $query->where('practitioner_id', $practitionerId);
        } elseif ($branchId) {
            // Get stock from specific branch (excluding practitioner inventories)
            $query->where('branch_id', $branchId)
                ->whereNull('practitioner_id');
        } else {
            // If no specific location, return total available stock across all locations
            // This is useful when branch/practitioner is not specified
            $totalStock = $this->inventoryReports()
                ->where('status', 'active')
                ->get()
                ->sum(function ($report) {
                    return $report->quantity_on_hand - $report->quantity_reserved;
                });

            return (float) $totalStock;
        }

        $report = $query->first();

        return $report ? (float) ($report->quantity_on_hand - $report->quantity_reserved) : 0.0;
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', InventoryItemStatus::ACTIVE);
    }

    /**
     * Scope for filtering by item type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('item_type', $type);
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereNotNull('reorder_point')
            ->whereHas('inventoryReports', function ($q) {
                $q->whereRaw('(quantity_on_hand - quantity_reserved) <= reorder_point');
            });
    }

    /**
     * Export as FHIR InventoryItem resource
     */
    public function toFhirResource(): array
    {
        return [
            'resourceType' => 'InventoryItem',
            'id' => $this->fhir_id,
            'status' => $this->status->value,
            'category' => $this->category,
            'code' => $this->code,
            'name' => [
                [
                    'name' => $this->name,
                    'language' => 'en',
                ],
            ],
            'description' => [
                'description' => $this->description,
                'language' => 'en',
            ],
            'characteristic' => $this->characteristic,
        ];
    }
}
