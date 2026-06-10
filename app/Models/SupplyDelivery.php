<?php

namespace App\Models;

use App\Enums\InventoryTransactionType;
use App\Enums\SupplyDeliveryStatus;
use App\Enums\SupplyReturnReason;
use App\Models\Scopes\SupplyDeliveryScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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

    public function supplyReturns(): HasMany
    {
        return $this->hasMany(SupplyReturn::class);
    }

    /**
     * Check if this delivery has any returns
     */
    public function hasReturns(): bool
    {
        return $this->supplyReturns()->exists();
    }

    /**
     * Get total quantity returned
     */
    public function getTotalQuantityReturned(): float
    {
        return (float) $this->supplyReturns()->sum('quantity_returned');
    }

    /**
     * Get net quantity (dispensed - returned)
     */
    public function getNetQuantity(): float
    {
        return $this->supplied_quantity - $this->getTotalQuantityReturned();
    }

    /**
     * Process a supply return
     *
     * @throws \Exception
     */
    public function returnSupply(
        float $quantityToReturn,
        SupplyReturnReason $reason,
        ?string $notes = null
    ): SupplyReturn {
        DB::beginTransaction();

        try {
            // Validations
            $alreadyReturned = $this->getTotalQuantityReturned();
            $availableToReturn = $this->supplied_quantity - $alreadyReturned;

            if ($quantityToReturn <= 0) {
                throw new \Exception('La cantidad a devolver debe ser mayor a 0');
            }

            if ($quantityToReturn > $availableToReturn) {
                throw new \Exception("Solo puedes devolver hasta {$availableToReturn} unidades. Ya se devolvieron {$alreadyReturned} unidades.");
            }

            // Determine inventory location to return to
            $practitioner = $this->practitioner;
            $returnToBranchId = null;
            $returnToPractitionerId = null;

            if ($practitioner->has_individual_inventory) {
                $returnToPractitionerId = $practitioner->id;
            } else {
                $returnToBranchId = $this->branch_id;
            }

            // Get the inventory report to restore stock
            $inventoryReport = InventoryReport::where('inventory_item_id', $this->inventory_item_id)
                ->where('branch_id', $returnToBranchId)
                ->where('practitioner_id', $returnToPractitionerId)
                ->first();

            if (! $inventoryReport) {
                // Create inventory report if it doesn't exist
                $inventoryReport = InventoryReport::create([
                    'inventory_item_id' => $this->inventory_item_id,
                    'client_id' => $this->client_id,
                    'branch_id' => $returnToBranchId,
                    'practitioner_id' => $returnToPractitionerId,
                    'quantity_on_hand' => 0,
                    'quantity_reserved' => 0,
                    'lot_number' => $this->lot_number,
                    'serial_number' => $this->serial_number,
                    'expiration_date' => $this->expiration_date,
                    'status' => 'active',
                ]);
            }

            $quantityBefore = $inventoryReport->quantity_on_hand;

            // Restore stock
            $inventoryReport->increment('quantity_on_hand', $quantityToReturn);

            // Create RETURN inventory transaction
            InventoryTransaction::create([
                'inventory_item_id' => $this->inventory_item_id,
                'transaction_type' => InventoryTransactionType::RETURN,
                'transaction_date' => now(),
                'quantity_change' => $quantityToReturn,
                'unit_of_measure' => $this->unit_of_measure,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityBefore + $quantityToReturn,
                'patient_id' => $this->patient_id,
                'encounter_id' => $this->encounter_id,
                'supply_request_id' => $this->based_on_supply_request_id,
                'supply_delivery_id' => $this->id,
                'to_location_client_id' => $this->client_id,
                'to_location_branch_id' => $returnToBranchId,
                'to_location_practitioner_id' => $returnToPractitionerId,
                'lot_number' => $this->lot_number,
                'serial_number' => $this->serial_number,
                'expiration_date' => $this->expiration_date,
                'performed_by_user_id' => auth()->id(),
                'reason' => $reason->label(),
                'notes' => $notes,
                'client_id' => $this->client_id,
            ]);

            // Handle charge item reversal if exists
            // Find ChargeItem using supporting_information JSON field
            $chargeItem = null;
            $amountReversed = null;

            // Attempt to find related charge item by supporting_information
            // ChargeItems may not always exist for supply requests
            try {
                // First try to find by supply_delivery_id in supporting_information
                $chargeItem = ChargeItem::where('encounter_id', $this->encounter_id)
                    ->where('patient_id', $this->patient_id)
                    ->whereIn('status', ['billable', 'planned', 'billed'])
                    ->whereJsonContains('supporting_information->supply_delivery_id', $this->id)
                    ->first();

                // If not found, try by supply_request_id
                if (! $chargeItem && $this->based_on_supply_request_id) {
                    $chargeItem = ChargeItem::where('encounter_id', $this->encounter_id)
                        ->where('patient_id', $this->patient_id)
                        ->whereIn('status', ['billable', 'planned', 'billed'])
                        ->whereJsonContains('supporting_information->supply_request_id', $this->based_on_supply_request_id)
                        ->first();
                }
            } catch (\Exception $e) {
                \Log::warning('Could not find ChargeItem for supply return', [
                    'delivery_id' => $this->id,
                    'encounter_id' => $this->encounter_id,
                    'error' => $e->getMessage(),
                ]);
            }

            if ($chargeItem && $chargeItem->status !== 'not-billable') {
                // Calculate proportional amount to reverse
                $unitPrice = $chargeItem->unit_price_value;
                $amountReversed = $unitPrice * $quantityToReturn;

                // If full return, mark as not billable
                if ($quantityToReturn == $this->supplied_quantity) {
                    $chargeItem->update([
                        'status' => 'not-billable',
                        'note' => ($chargeItem->note ?? '')."\nDevuelto completamente: {$reason->label()}. ".($notes ?? ''),
                    ]);

                    // Soft delete related invoice line items for full returns
                    \DB::table('invoice_line_items')
                        ->where('charge_item_id', $chargeItem->id)
                        ->whereNull('deleted_at')
                        ->update([
                            'deleted_at' => now(),
                            'updated_at' => now(),
                        ]);
                } else {
                    // Partial return - adjust quantity
                    $newQuantity = $chargeItem->quantity - $quantityToReturn;

                    $chargeItem->update([
                        'quantity' => max(0, $newQuantity),
                        'note' => ($chargeItem->note ?? '')."\nDevolución parcial de {$quantityToReturn} unidades: {$reason->label()}. ".($notes ?? ''),
                    ]);

                    // Update invoice line items for partial returns
                    $invoiceLineItems = \DB::table('invoice_line_items')
                        ->where('charge_item_id', $chargeItem->id)
                        ->whereNull('deleted_at')
                        ->get();

                    foreach ($invoiceLineItems as $lineItem) {
                        $newLineQuantity = max(0, $lineItem->quantity - $quantityToReturn);
                        $newLineTotalPretax = $newLineQuantity * $lineItem->unit_price;
                        $newLineTaxAmount = $newLineTotalPretax * $lineItem->tax_rate;
                        $newLineTotalGross = $newLineTotalPretax + $newLineTaxAmount - $lineItem->discount_amount;

                        \DB::table('invoice_line_items')
                            ->where('id', $lineItem->id)
                            ->update([
                                'quantity' => $newLineQuantity,
                                'line_total_pretax' => $newLineTotalPretax,
                                'line_total_tax' => $newLineTaxAmount,
                                'line_total_gross' => max(0, $newLineTotalGross),
                                'updated_at' => now(),
                            ]);
                    }
                }

                // Recalculate invoice totals
                $invoiceIds = \DB::table('invoice_line_items')
                    ->where('charge_item_id', $chargeItem->id)
                    ->pluck('invoice_id')
                    ->unique();

                foreach ($invoiceIds as $invoiceId) {
                    $this->recalculateInvoiceTotals($invoiceId);
                }
            }

            // Create supply return record
            $supplyReturn = SupplyReturn::create([
                'supply_delivery_id' => $this->id,
                'supply_request_id' => $this->based_on_supply_request_id,
                'inventory_item_id' => $this->inventory_item_id,
                'patient_id' => $this->patient_id,
                'encounter_id' => $this->encounter_id,
                'quantity_returned' => $quantityToReturn,
                'quantity_originally_dispensed' => $this->supplied_quantity,
                'unit_of_measure' => $this->unit_of_measure,
                'reason' => $reason,
                'notes' => $notes,
                'lot_number' => $this->lot_number,
                'serial_number' => $this->serial_number,
                'expiration_date' => $this->expiration_date,
                'returned_to_branch_id' => $returnToBranchId,
                'returned_to_practitioner_id' => $returnToPractitionerId,
                'returned_by_user_id' => auth()->id(),
                'returned_at' => now(),
                'charge_item_id' => $chargeItem?->id,
                'amount_reversed' => $amountReversed,
            ]);

            DB::commit();

            return $supplyReturn;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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

    /**
     * Recalculate invoice totals based on current line items
     */
    protected function recalculateInvoiceTotals(int $invoiceId): void
    {
        $totals = \DB::table('invoice_line_items')
            ->where('invoice_id', $invoiceId)
            ->whereNull('deleted_at')
            ->selectRaw('
                SUM(line_total_pretax) as subtotal_amount,
                SUM(line_total_tax) as total_tax,
                SUM(line_total_gross) as total_gross
            ')
            ->first();

        $subtotalAmount = $totals->subtotal_amount ?? 0;
        $totalTax = $totals->total_tax ?? 0;
        $totalGross = $totals->total_gross ?? 0;

        \DB::table('invoices')
            ->where('id', $invoiceId)
            ->update([
                'subtotal_amount' => $subtotalAmount,
                'total_preTax' => $subtotalAmount,
                'tax_amount' => $totalTax,
                'total_tax' => $totalTax,
                'total_amount' => $totalGross,
                'total_gross' => $totalGross,
                'total_net' => $totalGross,
                'updated_at' => now(),
            ]);
    }
}
