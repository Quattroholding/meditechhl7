<?php

namespace App\Services\DocumentProcessors;

use App\Enums\InventoryItemStatus;
use App\Enums\InventoryTransactionType;
use App\Models\DocumentUpload;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryDocumentProcessor extends BaseDocumentProcessor
{
    /**
     * Process an approved inventory document
     */
    public function process(DocumentUpload $document): bool
    {
        try {
            $this->logStep('Starting inventory document processing', [
                'document_id' => $document->id,
                'client_id' => $document->client_id,
            ]);

            // Validate document is approved
            if ($document->approval()->where('approved_at', '!=', null)->doesntExist()) {
                throw new \RuntimeException('Document is not approved');
            }

            // Get parsed data
            $parseResult = $document->parseResult;
            if (! $parseResult) {
                throw new \RuntimeException('No parsing result found for document');
            }

            // Decode extracted_data if it's stored as JSON string
            $extractedData = $parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }

            $items = $extractedData['items'] ?? [];

            if (empty($items)) {
                throw new \RuntimeException('No items to process in extracted data');
            }

            // Process within a transaction for atomicity
            DB::transaction(function () use ($document, $items) {
                foreach ($items as $index => $item) {
                    $this->processItem($document, $item, $index);
                }

                // Update document status
                $document->markAsProcessed();
            });

            $this->logStep('Inventory document processed successfully', [
                'document_id' => $document->id,
                'items_count' => count($items),
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logError('Inventory processing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Process a single inventory item
     */
    private function processItem(DocumentUpload $document, array $item, int $index): void
    {
        $sku = $item['sku'] ?? null;
        $name = $item['name'] ?? null;
        $quantity = (float) ($item['quantity'] ?? 0);
        $unitCost = (float) ($item['unit_cost'] ?? 0);

        // Track both presentation and internal units
        $unitType = $item['unit_type'] ?? 'presentation';
        $conversionFactor = 1.0;
        if ($unitType === 'internal' && isset($item['internal_units_per_presentation'])) {
            $conversionFactor = (float) $item['internal_units_per_presentation'];
        }

        // Store quantities: presentation units and internal units
        $quantityInPresentations = $quantity;
        $quantityInInternalUnits = $quantity * $conversionFactor;

        if (! $sku || ! $name) {
            $this->logError("Item {$index}: Missing SKU or name", ['item' => $item]);
            throw new \RuntimeException("Item {$index}: Missing required fields (SKU, name)");
        }

        // Check if inventory item already exists
        $inventoryItem = InventoryItem::where('client_id', $document->client_id)
            ->where('sku', $sku)
            ->first();

        if (! $inventoryItem) {
            // Create new inventory item
            $itemData = [
                'client_id' => $document->client_id,
                'sku' => $sku,
                'name' => $name,
                'status' => InventoryItemStatus::ACTIVE,
                'item_type' => 'supply', // Default type
                'base_cost' => $unitCost,
                'base_price' => $this->calculatePrice($unitCost),
                'category' => isset($item['category']) ? [$item['category']] : [],
                'unit_of_measure' => $item['unit'] ?? 'unit',
                'requires_prescription' => false,
                'track_by_lot' => false,
                'track_by_serial' => false,
                'expiration_tracking' => false,
                'reorder_point' => 10,
                'reorder_quantity' => 20,
            ];

            // Add internal tracking info if applicable
            if ($unitType === 'internal') {
                $itemData['track_internal_content'] = true;
                $itemData['internal_unit'] = $item['unit'] ?? 'unit';
                $itemData['internal_units_per_presentation'] = (float) ($item['internal_units_per_presentation'] ?? 1);
            }

            $inventoryItem = InventoryItem::create($itemData);

            $this->logStep('Created new inventory item', [
                'item_id' => $inventoryItem->id,
                'sku' => $sku,
            ]);
        }

        // Get the user who approved the document
        $approval = $document->approval()->firstOrFail();
        $performedByUserId = $approval->approved_by_user_id;

        // Get quantity before update from existing transactions
        $existingQuantity = InventoryTransaction::where('client_id', $document->client_id)
            ->where('inventory_item_id', $inventoryItem->id)
            ->sum('quantity_change');

        $quantityBefore = (float) $existingQuantity;
        $quantityAfter = $quantityBefore + $quantityInInternalUnits;
        $internalUnitsBefore = $quantityBefore;
        $internalUnitsAfter = $quantityAfter;
        $totalCost = $quantityInPresentations * $unitCost;

        // Determine the unit of measure for the transaction
        $transactionUnitOfMeasure = $unitType === 'internal' ? ($item['unit'] ?? 'unit') : ($item['unit'] ?? 'unit');

        // Create inventory transaction (store in internal units)
        $transaction = InventoryTransaction::create([
            'client_id' => $document->client_id,
            'inventory_item_id' => $inventoryItem->id,
            'transaction_type' => InventoryTransactionType::PURCHASE,
            'transaction_date' => now(),
            'quantity_change' => $quantityInInternalUnits,
            'quantity_before' => $internalUnitsBefore,
            'quantity_after' => $internalUnitsAfter,
            'unit_of_measure' => $transactionUnitOfMeasure,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'reason' => 'Imported from PDF document',
            'notes' => "Document: {$document->original_filename}",
            'performed_by_user_id' => $performedByUserId,
        ]);

        // Calculate quantity in presentations for inventory_reports
        $quantityOnHandAfter = $quantityAfter / $conversionFactor;

        // Update or create inventory report with new quantity using direct SQL to bypass all scopes
        $reportExists = DB::table('inventory_reports')
            ->where('client_id', $document->client_id)
            ->where('inventory_item_id', $inventoryItem->id)
            ->whereNull('branch_id')
            ->whereNull('practitioner_id')
            ->exists();

        if ($reportExists) {
            // Update existing report
            DB::table('inventory_reports')
                ->where('client_id', $document->client_id)
                ->where('inventory_item_id', $inventoryItem->id)
                ->whereNull('branch_id')
                ->whereNull('practitioner_id')
                ->update([
                    'quantity_on_hand' => $quantityOnHandAfter,
                    'internal_units_on_hand' => $quantityAfter,
                    'quantity_reserved' => 0,
                    'status' => 'active',
                    'updated_at' => now(),
                ]);
        } else {
            // Create new report
            DB::table('inventory_reports')->insert([
                'fhir_id' => 'inventory-report-'.Str::uuid(),
                'client_id' => $document->client_id,
                'inventory_item_id' => $inventoryItem->id,
                'branch_id' => null,
                'practitioner_id' => null,
                'quantity_on_hand' => $quantityOnHandAfter,
                'internal_units_on_hand' => $quantityAfter,
                'quantity_reserved' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->logStep('Created transaction for item', [
            'item_id' => $inventoryItem->id,
            'transaction_id' => $transaction->id,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
        ]);
    }

    /**
     * Calculate selling price from unit cost (simple markup)
     */
    private function calculatePrice(float $unitCost): float
    {
        // Apply 30% markup by default
        return round($unitCost * 1.30, 2);
    }
}
