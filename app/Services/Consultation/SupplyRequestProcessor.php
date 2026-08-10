<?php

namespace App\Services\Consultation;

use App\Enums\ChargeItemStatus;
use App\Enums\InventoryTransactionType;
use App\Enums\SupplyDeliveryStatus;
use App\Enums\SupplyRequestStatus;
use App\Models\ChargeItem;
use App\Models\Encounter;
use App\Models\InventoryReport;
use App\Models\InventoryTransaction;
use App\Models\SupplyDelivery;
use App\Models\SupplyRequest;
use Illuminate\Support\Facades\DB;

class SupplyRequestProcessor
{
    /**
     * Procesar todos los supply requests en estado DRAFT (OPERACIÓN ATÓMICA)
     *
     * @return array{processed: int, charge_items_created: int}
     *
     * @throws \Exception si falla validación de inventario
     */
    public function processSupplyRequests(Encounter $encounter): array
    {
        $supplyRequests = $encounter->supplyRequests()
            ->where('status', SupplyRequestStatus::DRAFT)
            ->with(['inventoryItem', 'practitioner'])
            ->get();

        if ($supplyRequests->isEmpty()) {
            return ['processed' => 0, 'charge_items_created' => 0];
        }

        $stats = ['processed' => 0, 'charge_items_created' => 0];

        DB::transaction(function () use ($supplyRequests, $encounter, &$stats) {
            foreach ($supplyRequests as $supplyRequest) {
                $this->processSingleSupplyRequest($supplyRequest, $encounter);
                $stats['processed']++;

                // Contar si se creó ChargeItem
                if ($supplyRequest->is_billable && ! $supplyRequest->is_free) {
                    $stats['charge_items_created']++;
                }
            }
        });

        return $stats;
    }

    /**
     * Procesar un único supply request
     *
     * @throws \Exception si falla validación o creación
     */
    protected function processSingleSupplyRequest(SupplyRequest $supplyRequest, Encounter $encounter): void
    {
        // 1. Determinar ubicación de inventario
        $practitioner = $supplyRequest->practitioner;
        $inventoryReport = InventoryReport::getForPractitioner(
            inventoryItemId: $supplyRequest->inventory_item_id,
            practitioner: $practitioner,
            branchId: $supplyRequest->branch_id
        );

        // 2. Validar stock
        if (! $inventoryReport) {
            throw new \Exception(
                "No hay inventario configurado para {$supplyRequest->inventoryItem->name}"
            );
        }

        if ($inventoryReport->quantity_available < $supplyRequest->quantity) {
            throw new \Exception(
                "Stock insuficiente para {$supplyRequest->inventoryItem->name}. ".
                "Disponible: {$inventoryReport->quantity_available}, ".
                "Solicitado: {$supplyRequest->quantity}"
            );
        }

        // 3. Crear SupplyDelivery
        $delivery = SupplyDelivery::create([
            'based_on_supply_request_id' => $supplyRequest->id,
            'inventory_item_id' => $supplyRequest->inventory_item_id,
            'supplied_quantity' => $supplyRequest->quantity,
            'unit_of_measure' => $supplyRequest->unit_of_measure,
            'lot_number' => $inventoryReport->lot_number,
            'serial_number' => $inventoryReport->serial_number,
            'expiration_date' => $inventoryReport->expiration_date,
            'patient_id' => $supplyRequest->patient_id,
            'encounter_id' => $supplyRequest->encounter_id,
            'practitioner_id' => auth()->user()->practitioner->id,
            'occurrence_datetime' => now(),
            'status' => SupplyDeliveryStatus::COMPLETED,
            'client_id' => $supplyRequest->client_id,
            'branch_id' => $inventoryReport->branch_id,
            'practitioner_inventory_id' => $inventoryReport->practitioner_id,
        ]);

        // 4. Deducir stock
        $quantityBefore = $inventoryReport->quantity_on_hand;
        $inventoryReport->decrement('quantity_on_hand', $supplyRequest->quantity);
        $inventoryReport->refresh();
        $quantityAfter = $inventoryReport->quantity_on_hand;

        // 5. Registrar transacción
        InventoryTransaction::create([
            'transaction_type' => InventoryTransactionType::SUPPLY_DELIVERY,
            'transaction_date' => now(),
            'inventory_item_id' => $supplyRequest->inventory_item_id,
            'quantity_change' => -$supplyRequest->quantity,
            'unit_of_measure' => $supplyRequest->unit_of_measure,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'from_location_client_id' => $supplyRequest->client_id,
            'from_location_branch_id' => $inventoryReport->branch_id,
            'from_location_practitioner_id' => $inventoryReport->practitioner_id,
            'supply_request_id' => $supplyRequest->id,
            'supply_delivery_id' => $delivery->id,
            'patient_id' => $supplyRequest->patient_id,
            'encounter_id' => $supplyRequest->encounter_id,
            'performed_by_user_id' => auth()->id(),
            'reason' => "Suministro entregado al paciente durante encuentro {$encounter->identifier}",
            'client_id' => $supplyRequest->client_id,
        ]);

        // 6. Crear ChargeItem si es cobrable
        if ($supplyRequest->is_billable && ! $supplyRequest->is_free) {
            $item = $supplyRequest->inventoryItem;
            $unitPrice = $supplyRequest->custom_price ?? $item->base_price;

            ChargeItem::create([
                'status' => ChargeItemStatus::BILLABLE,
                'code' => [
                    'coding' => [[
                        'system' => 'urn:meditech:inventory',
                        'code' => $item->sku,
                        'display' => $item->name,
                    ]],
                    'text' => $item->name,
                ],
                'patient_id' => $supplyRequest->patient_id,
                'encounter_id' => $supplyRequest->encounter_id,
                'appointment_id' => $encounter->appointment_id,
                'performer_practitioner_id' => $supplyRequest->practitioner_id,
                'performer_organization_id' => $supplyRequest->client_id,
                'occurrence_date_time' => now(),
                'quantity' => $supplyRequest->quantity,
                'unit_price_value' => $unitPrice,
                'unit_price_currency' => $item->currency,
                'product_reference' => [
                    'reference' => "InventoryItem/{$item->fhir_id}",
                ],
                'supporting_information' => [
                    'supply_request_id' => $supplyRequest->id,
                    'supply_delivery_id' => $delivery->id,
                ],
                'client_id' => $supplyRequest->client_id,
            ]);
        }

        // 7. Marcar SupplyRequest como completado
        $supplyRequest->update(['status' => SupplyRequestStatus::COMPLETED]);
    }
}
