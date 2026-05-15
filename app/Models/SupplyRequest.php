<?php

namespace App\Models;

use App\Enums\SupplyDeliveryStatus;
use App\Enums\SupplyRequestStatus;
use App\Models\Scopes\SupplyRequestScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplyRequest extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id', 'identifier', 'status', 'intent', 'priority', 'inventory_item_id',
        'quantity', 'unit_of_measure', 'patient_id', 'encounter_id', 'practitioner_id',
        'is_billable', 'is_free', 'custom_price', 'reason_code', 'reason_reference',
        'occurrence_datetime', 'client_id', 'branch_id', 'consulting_room_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => SupplyRequestStatus::class,
            'quantity' => 'decimal:2',
            'custom_price' => 'decimal:2',
            'is_billable' => 'boolean',
            'is_free' => 'boolean',
            'reason_code' => 'array',
            'occurrence_datetime' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new SupplyRequestScope);

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'supply-request-'.Str::uuid();
            }
            if (empty($model->identifier)) {
                $model->identifier = 'SR-'.strtoupper(Str::random(8));
            }
        });
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

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function consultingRoom(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class);
    }

    public function supplyDelivery(): HasOne
    {
        return $this->hasOne(SupplyDelivery::class, 'based_on_supply_request_id');
    }

    public function chargeItem(): HasOne
    {
        return $this->hasOne(ChargeItem::class, 'supporting_information->supply_request_id');
    }

    /**
     * Fulfill this supply request by creating a delivery and deducting stock
     */
    public function fulfill(float $quantity, ?string $lotNumber = null, ?string $serialNumber = null): SupplyDelivery
    {
        $inventoryReport = InventoryReport::getForPractitioner(
            inventoryItemId: $this->inventory_item_id,
            practitioner: $this->practitioner,
            branchId: $this->branch_id
        );

        if (! $inventoryReport) {
            throw new \Exception("No inventory report found for {$this->inventoryItem->name}");
        }

        if ($inventoryReport->quantity_available < $quantity) {
            throw new \Exception(
                "Insufficient stock for {$this->inventoryItem->name}. ".
                "Available: {$inventoryReport->quantity_available}, Requested: {$quantity}"
            );
        }

        // Create supply delivery
        $delivery = SupplyDelivery::create([
            'based_on_supply_request_id' => $this->id,
            'inventory_item_id' => $this->inventory_item_id,
            'supplied_quantity' => $quantity,
            'unit_of_measure' => $this->unit_of_measure,
            'lot_number' => $lotNumber ?? $inventoryReport->lot_number,
            'serial_number' => $serialNumber ?? $inventoryReport->serial_number,
            'expiration_date' => $inventoryReport->expiration_date,
            'patient_id' => $this->patient_id,
            'encounter_id' => $this->encounter_id,
            'practitioner_id' => auth()->user()->practitioner->id,
            'occurrence_datetime' => now(),
            'status' => SupplyDeliveryStatus::COMPLETED,
            'client_id' => $this->client_id,
            'branch_id' => $inventoryReport->branch_id,
            'practitioner_inventory_id' => $inventoryReport->practitioner_id,
        ]);

        // Deduct stock
        $inventoryReport->decrement('quantity_on_hand', $quantity);

        // Mark supply request as completed
        $this->update(['status' => SupplyRequestStatus::COMPLETED]);

        return $delivery;
    }

    /**
     * Export as FHIR SupplyRequest resource
     */
    public function toFhirResource(): array
    {
        return [
            'resourceType' => 'SupplyRequest',
            'id' => $this->fhir_id,
            'identifier' => [
                [
                    'system' => 'urn:meditech:supply-request',
                    'value' => $this->identifier,
                ],
            ],
            'status' => $this->status->value,
            'intent' => $this->intent,
            'priority' => $this->priority,
            'itemReference' => [
                'reference' => "InventoryItem/{$this->inventoryItem->fhir_id}",
            ],
            'quantity' => [
                'value' => (float) $this->quantity,
                'unit' => $this->unit_of_measure,
            ],
            'occurrenceDateTime' => $this->occurrence_datetime?->toIso8601String(),
            'requester' => [
                'reference' => "Practitioner/{$this->practitioner->fhir_id}",
            ],
            'deliverFor' => [
                'reference' => "Patient/{$this->patient->fhir_id}",
            ],
        ];
    }
}
