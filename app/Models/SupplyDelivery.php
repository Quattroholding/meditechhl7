<?php

namespace App\Models;

use App\Enums\SupplyDeliveryStatus;
use App\Models\Scopes\SupplyDeliveryScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplyDelivery extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id', 'identifier', 'status', 'based_on_supply_request_id',
        'inventory_item_id', 'supplied_quantity', 'unit_of_measure', 'lot_number',
        'serial_number', 'expiration_date', 'patient_id', 'encounter_id',
        'practitioner_id', 'occurrence_datetime', 'client_id', 'branch_id',
        'practitioner_inventory_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => SupplyDeliveryStatus::class,
            'supplied_quantity' => 'decimal:2',
            'expiration_date' => 'date',
            'occurrence_datetime' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new SupplyDeliveryScope);

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'supply-delivery-'.Str::uuid();
            }
            if (empty($model->identifier)) {
                $model->identifier = 'SD-'.strtoupper(Str::random(8));
            }
        });
    }

    public function supplyRequest(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class, 'based_on_supply_request_id');
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

    public function practitionerInventory(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'practitioner_inventory_id');
    }

    /**
     * Export as FHIR SupplyDelivery resource
     */
    public function toFhirResource(): array
    {
        return [
            'resourceType' => 'SupplyDelivery',
            'id' => $this->fhir_id,
            'identifier' => [
                [
                    'system' => 'urn:meditech:supply-delivery',
                    'value' => $this->identifier,
                ],
            ],
            'status' => $this->status->value,
            'basedOn' => $this->based_on_supply_request_id ? [
                [
                    'reference' => "SupplyRequest/{$this->supplyRequest->fhir_id}",
                ],
            ] : null,
            'suppliedItem' => [
                'itemReference' => [
                    'reference' => "InventoryItem/{$this->inventoryItem->fhir_id}",
                ],
                'quantity' => [
                    'value' => (float) $this->supplied_quantity,
                    'unit' => $this->unit_of_measure,
                ],
            ],
            'occurrenceDateTime' => $this->occurrence_datetime->toIso8601String(),
            'supplier' => [
                'reference' => "Practitioner/{$this->practitioner->fhir_id}",
            ],
            'destination' => [
                'reference' => "Patient/{$this->patient->fhir_id}",
            ],
        ];
    }
}
