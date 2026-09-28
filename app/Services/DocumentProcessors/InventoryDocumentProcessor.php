<?php

namespace App\Services\DocumentProcessors;

use App\Enums\InventoryItemStatus;
use App\Enums\InventoryTransactionType;
use App\Models\DocumentUpload;
use App\Models\InventoryItem;
use App\Models\InventoryReport;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;

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

            $items = $parseResult->extracted_data['items'] ?? [];

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
        $quantity = $item['quantity'] ?? 0;
        $unitCost = $item['unit_cost'] ?? 0;

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
            $inventoryItem = InventoryItem::create([
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
            ]);

            $this->logStep('Created new inventory item', [
                'item_id' => $inventoryItem->id,
                'sku' => $sku,
            ]);
        }

        // Create inventory transaction
        $quantityBefore = $inventoryItem->current_quantity ?? 0;
        $quantityAfter = $quantityBefore + $quantity;
        $totalCost = $quantity * $unitCost;

        $transaction = InventoryTransaction::create([
            'client_id' => $document->client_id,
            'inventory_item_id' => $inventoryItem->id,
            'transaction_type' => InventoryTransactionType::PURCHASE,
            'transaction_date' => now(),
            'quantity_change' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'reason' => 'Imported from PDF document',
            'notes' => "Document: {$document->original_filename}",
            'performed_by_user_id' => auth()->id(),
        ]);

        // Update inventory item quantity
        $inventoryItem->update([
            'current_quantity' => $quantityAfter,
        ]);

        // Update or create inventory report
        $report = InventoryReport::firstOrCreate(
            [
                'client_id' => $document->client_id,
                'inventory_item_id' => $inventoryItem->id,
            ],
            [
                'total_quantity' => 0,
                'total_cost' => 0,
            ]
        );

        $report->update([
            'total_quantity' => $report->total_quantity + $quantity,
            'total_cost' => $report->total_cost + $totalCost,
        ]);

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
