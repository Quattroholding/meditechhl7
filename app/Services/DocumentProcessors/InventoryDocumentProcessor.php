<?php

namespace App\Services\DocumentProcessors;

use App\Enums\InventoryItemStatus;
use App\Enums\InventoryTransactionType;
use App\Models\DocumentUpload;
use App\Models\Finance\Supplier;
use App\Models\Finance\SupplierInvoice;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Services\Accounting\AccountingEngineService;
use Carbon\Carbon;
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

            $this->logStep('Extracted data after decoding', [
                'is_array' => is_array($extractedData),
                'has_supplier_name' => isset($extractedData['supplier_name']),
                'supplier_name' => $extractedData['supplier_name'] ?? 'NOT SET',
                'supplier_ruc' => $extractedData['supplier_ruc'] ?? 'NOT SET',
                'supplier_dv' => $extractedData['supplier_dv'] ?? 'NOT SET',
                'items_count' => count($extractedData['items'] ?? []),
            ]);

            $items = $extractedData['items'] ?? [];

            if (empty($items)) {
                throw new \RuntimeException('No items to process in extracted data');
            }

            // Process within a transaction for atomicity
            DB::transaction(function () use ($document, $items, $extractedData) {
                foreach ($items as $index => $item) {
                    $this->processItem($document, $item, $index);
                }

                // Process supplier invoice if supplier information is available
                if (! empty($extractedData['supplier_name'])) {
                    $this->processSupplierInvoice($document, $extractedData);
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

        // Log the item data being processed
        $this->logStep('Processing item from document', [
            'document_id' => $document->id,
            'item_index' => $index,
            'sku' => $sku,
            'name' => $name,
            'quantity' => $quantity,
            'unit_type' => $unitType,
            'internal_units_per_presentation_from_item' => $item['internal_units_per_presentation'] ?? 'NOT_SET',
            'conversion_factor_calculated' => $conversionFactor,
            'full_item_data' => $item,
        ]);

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
            // Normalize unit of measure value
            $normalizedUnit = $this->normalizeUnitOfMeasure($item['unit'] ?? null);

            // Use base_price from document if available, otherwise use unit_cost
            $basePrice = (float) ($item['base_price'] ?? $unitCost);

            // Create new inventory item
            $itemData = [
                'client_id' => $document->client_id,
                'sku' => $sku,
                'name' => $name,
                'status' => InventoryItemStatus::ACTIVE,
                'item_type' => 'supply', // Default type
                'base_cost' => $unitCost,
                'base_price' => $basePrice,
                'category' => isset($item['category']) ? [$item['category']] : [],
                'unit_of_measure' => $normalizedUnit,
                'requires_prescription' => false,
                'track_by_lot' => false,
                'track_by_serial' => false,
                'expiration_tracking' => false,
                'reorder_point' => 1,
                'reorder_quantity' => 1,
            ];

            // Add internal tracking info if applicable
            if ($unitType === 'internal') {
                $itemData['track_internal_content'] = true;
                $itemData['internal_unit'] = $normalizedUnit;
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

        // Get existing quantities from inventory_reports to ensure consistent calculation
        $report = DB::table('inventory_reports')
            ->where('client_id', $document->client_id)
            ->where('inventory_item_id', $inventoryItem->id)
            ->where('branch_id', $document->branch_id)
            ->whereNull('practitioner_id')
            ->first();

        // Calculate quantity before
        $quantityOnHandBefore = $report?->quantity_on_hand ?? 0;
        $internalUnitsOnHandBefore = $report?->internal_units_on_hand ?? 0;

        // Quantity after = previous + new (in their respective units)
        $quantityOnHandAfter = $quantityOnHandBefore + $quantityInPresentations;
        $internalUnitsAfter = $internalUnitsOnHandBefore + $quantityInInternalUnits;
        $totalCost = $quantityInPresentations * $unitCost;

        // Normalize unit of measure for the transaction (reuse if already normalized above, otherwise normalize here)
        $transactionUnitOfMeasure = isset($normalizedUnit) ? $normalizedUnit : $this->normalizeUnitOfMeasure($item['unit'] ?? null);

        // Create inventory transaction (store in internal units)
        $transaction = InventoryTransaction::create([
            'client_id' => $document->client_id,
            'inventory_item_id' => $inventoryItem->id,
            'transaction_type' => InventoryTransactionType::PURCHASE,
            'transaction_date' => now(),
            'quantity_change' => $quantityInInternalUnits,
            'quantity_before' => $internalUnitsOnHandBefore,
            'quantity_after' => $internalUnitsAfter,
            'unit_of_measure' => $transactionUnitOfMeasure,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'reason' => 'Imported from PDF document',
            'notes' => "Document: {$document->original_filename}",
            'performed_by_user_id' => $performedByUserId,
        ]);

        // Update or create inventory report with new quantity using direct SQL to bypass all scopes
        if ($report) {
            // Update existing report
            DB::table('inventory_reports')
                ->where('client_id', $document->client_id)
                ->where('inventory_item_id', $inventoryItem->id)
                ->where('branch_id', $document->branch_id)
                ->whereNull('practitioner_id')
                ->update([
                    'quantity_on_hand' => $quantityOnHandAfter,
                    'internal_units_on_hand' => $internalUnitsAfter,
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
                'branch_id' => $document->branch_id,
                'practitioner_id' => null,
                'quantity_on_hand' => $quantityOnHandAfter,
                'internal_units_on_hand' => $internalUnitsAfter,
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
     * Process supplier invoice and generate accounting entry
     */
    private function processSupplierInvoice(DocumentUpload $document, array $extractedData): void
    {
        try {
            $supplierName = $extractedData['supplier_name'] ?? null;
            $supplierRuc = $extractedData['supplier_ruc'] ?? null;
            $supplierDv = $extractedData['supplier_dv'] ?? null;
            $invoiceNumber = $extractedData['invoice_number'] ?? null;
            $invoiceDate = $extractedData['invoice_date'] ?? null;
            $total = (float) ($extractedData['total'] ?? 0);
            $subtotal = (float) ($extractedData['subtotal'] ?? 0);
            $taxAmount = (float) ($extractedData['total_tax'] ?? 0);

            if (! $supplierName || ! $invoiceNumber) {
                $this->logStep('Skipping supplier invoice: Missing supplier name or invoice number', [
                    'supplier_name' => $supplierName,
                    'invoice_number' => $invoiceNumber,
                ]);

                return;
            }

            $this->logStep('Processing supplier invoice from document', [
                'document_id' => $document->id,
                'supplier_name' => $supplierName,
                'supplier_ruc' => $supplierRuc,
                'supplier_dv' => $supplierDv,
                'invoice_number' => $invoiceNumber,
                'total' => $total,
            ]);

            // Find or create supplier
            $supplier = $this->findOrCreateSupplier($document->client_id, $supplierName, $supplierRuc, $supplierDv);

            // Get the user who approved the document
            $approvalUser = $document->approval()?->where('approved_at', '!=', null)->first()?->approved_by_user_id;

            // Create supplier invoice record
            $supplierInvoice = $this->createSupplierInvoice(
                $document,
                $supplier,
                $invoiceNumber,
                $invoiceDate,
                $subtotal,
                $taxAmount,
                $total,
                $approvalUser
            );

            // Actualizar document_uploads con las relaciones creadas
            $document->update([
                'supplier_id' => $supplier->id,
                'supplier_invoice_id' => $supplierInvoice->id,
                'cost_center_id' => $supplierInvoice->cost_center_id, // Vincular el cost center si está disponible
            ]);

            // Generate accounting entry for the supplier invoice
            $this->generateAccountingEntry($document, $supplier, $supplierInvoice, $total);

            $this->logStep('Supplier invoice processed successfully', [
                'supplier_invoice_id' => $supplierInvoice->id,
                'supplier_id' => $supplier->id,
                'document_supplier_id' => $document->supplier_id,
                'document_supplier_invoice_id' => $document->supplier_invoice_id,
            ]);

        } catch (\Exception $e) {
            $this->logError('Error processing supplier invoice', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            // Don't throw - let inventory processing continue even if accounting fails
            // This is important to maintain inventory data integrity
        }
    }

    /**
     * Find or create a supplier by RUC (primary) or name (fallback)
     */
    private function findOrCreateSupplier(int $clientId, string $supplierName, ?string $supplierRuc = null, ?string $supplierDv = null): Supplier
    {
        // Try to find by RUC first (more reliable unique identifier)
        if ($supplierRuc) {
            $supplier = Supplier::where('client_id', $clientId)
                ->where('ruc', $supplierRuc)
                ->first();

            if ($supplier) {
                return $supplier;
            }
        }

        // Fall back to finding by name
        $supplier = Supplier::where('client_id', $clientId)
            ->where('legal_name', $supplierName)
            ->first();

        if ($supplier) {
            return $supplier;
        }

        $this->logStep('Creating new supplier', [
            'client_id' => $clientId,
            'supplier_name' => $supplierName,
            'supplier_ruc' => $supplierRuc,
            'supplier_dv' => $supplierDv,
        ]);

        // Create new supplier with minimal required data
        // Get the default CxP account
        $cxpAccount = DB::table('accounting_accounts')
            ->where('client_id', $clientId)
            ->where('code', '2101') // Cuentas por Pagar
            ->first();

        $accountingAccountId = $cxpAccount?->id;

        if (! $accountingAccountId) {
            // If CxP account doesn't exist, try to find any payable account
            $anyPayable = DB::table('accounting_accounts')
                ->where('client_id', $clientId)
                ->where('account_type', 'liability')
                ->first();

            if ($anyPayable) {
                $accountingAccountId = $anyPayable->id;
            }
        }

        return Supplier::create([
            'uuid' => Str::uuid(),
            'client_id' => $clientId,
            'ruc' => $supplierRuc,
            'dv' => $supplierDv,
            'legal_name' => $supplierName,
            'commercial_name' => $supplierName,
            'accounting_account_id' => $accountingAccountId,
            'status' => 'active',
            'created_by' => auth()->id() ?? 1,
            'updated_by' => auth()->id() ?? 1,
        ]);
    }

    /**
     * Create a supplier invoice record
     */
    private function createSupplierInvoice(
        DocumentUpload $document,
        Supplier $supplier,
        string $invoiceNumber,
        ?string $invoiceDate,
        float $subtotal,
        float $taxAmount,
        float $total,
        ?int $approvalUserId = null
    ): SupplierInvoice {
        // Parse invoice date
        $parsedDate = null;
        if ($invoiceDate) {
            try {
                $parsedDate = Carbon::parse($invoiceDate)->toDateString();
            } catch (\Exception $e) {
                $this->logStep('Could not parse invoice date', ['invoice_date' => $invoiceDate]);
                $parsedDate = now()->toDateString();
            }
        }

        // Calculate due date (default to 30 days from invoice date)
        $dueDate = $parsedDate ? Carbon::parse($parsedDate)->addDays(30)->toDateString() : now()->addDays(30)->toDateString();

        // Use approval user if available, otherwise use current auth user or fallback to 1
        $userId = $approvalUserId ?? auth()->id() ?? 1;

        return SupplierInvoice::create([
            'uuid' => Str::uuid(),
            'client_id' => $document->client_id,
            'supplier_id' => $supplier->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $parsedDate,
            'received_date' => now()->toDateString(),
            'due_date' => $dueDate,
            'currency' => 'PAB',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'paid_amount' => 0,
            'balance' => $total,
            'cost_center_id' => $document->cost_center_id, // Use document's cost center if available
            'document_path' => $document->file_path ?? null,
            'document_filename' => $document->original_filename ?? null,
            'notes' => "Imported from document: {$document->original_filename}",
            'status' => 'approved', // Mark as approved since the document is approved
            'approved_at' => now(),
            'approved_by' => $userId,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    /**
     * Generate accounting entry for the supplier invoice
     */
    private function generateAccountingEntry(
        DocumentUpload $document,
        Supplier $supplier,
        SupplierInvoice $supplierInvoice,
        float $amount
    ): void {
        try {
            $service = new AccountingEngineService(
                app('App\Services\Accounting\AccountingService'),
                app('App\Services\Accounting\JournalEntryService')
            );

            // Generate accounting entry using the supplier invoice
            $journalEntry = $service->processSupplierInvoiceCreated($supplierInvoice);

            // Link journal entry to supplier invoice and mark as approved
            $supplierInvoice->update([
                'journal_entry_id' => $journalEntry->id,
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id() ?? 1,
            ]);

            $this->logStep('Accounting entry created and supplier invoice approved', [
                'journal_entry_id' => $journalEntry->id,
                'supplier_invoice_id' => $supplierInvoice->id,
                'approved_by' => auth()->id() ?? 1,
            ]);

        } catch (\Exception $e) {
            $this->logError('Error generating accounting entry', [
                'supplier_invoice_id' => $supplierInvoice->id,
                'error' => $e->getMessage(),
            ]);

            // Don't rethrow - allow document processing to complete
            // Accounting entry can be generated manually later if needed
        }
    }

    /**
     * Normalize unit value to match valid UnitOfMeasure enum values
     * Maps common aliases and invalid values to valid enum backing values
     */
    private function normalizeUnitOfMeasure(?string $unit): string
    {
        if (! $unit) {
            return 'unit';
        }

        // Map common aliases to valid enum values
        $mapping = [
            'und' => 'unit',      // Common Spanish abbreviation for unidad
            'ud' => 'unit',       // Abbreviation used in enum symbol
            'unidad' => 'unit',
            'unidades' => 'unit',
            'units' => 'unit',
            'caja' => 'box',
            'cajas' => 'box',
            'viales' => 'vial',
            'mililitro' => 'ml',
            'mililitros' => 'ml',
            'miligramo' => 'mg',
            'miligramos' => 'mg',
            'gramo' => 'g',
            'gramos' => 'g',
        ];

        $normalized = strtolower(trim($unit));

        return $mapping[$normalized] ?? 'unit';
    }
}
