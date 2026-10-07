<?php

namespace App\Services\DocumentProcessors;

use App\Models\DocumentUpload;
use App\Models\Finance\Supplier;
use App\Models\Finance\SupplierInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierInvoiceProcessor extends BaseDocumentProcessor
{
    /**
     * Process an approved supplier invoice document
     */
    public function process(DocumentUpload $document): bool
    {
        try {
            $this->logStep('Starting supplier invoice document processing', [
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
            ]);

            // Process within a transaction for atomicity
            DB::transaction(function () use ($document, $extractedData) {
                // Process supplier invoice if supplier information is available
                if (! empty($extractedData['supplier_name'])) {
                    $this->processSupplierInvoice($document, $extractedData);
                } else {
                    throw new \RuntimeException('No supplier name found in extracted data');
                }

                // Update document status
                $document->markAsProcessed();
            });

            $this->logStep('Supplier invoice document processed successfully', [
                'document_id' => $document->id,
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logError('Supplier invoice processing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Process supplier invoice creation
     */
    private function processSupplierInvoice(DocumentUpload $document, array $extractedData): void
    {
        $supplierName = $extractedData['supplier_name'] ?? null;
        $supplierRuc = $extractedData['supplier_ruc'] ?? null;
        $supplierDv = $extractedData['supplier_dv'] ?? null;

        if (! $supplierName) {
            throw new \RuntimeException('Supplier name is required');
        }

        // Find or create supplier
        $supplier = $this->findOrCreateSupplier(
            $document->client_id,
            $supplierName,
            $supplierRuc,
            $supplierDv
        );

        // Create supplier invoice
        $supplierInvoice = $this->createSupplierInvoice(
            $document,
            $supplier,
            $extractedData
        );

        // Update document with reference
        $document->update([
            'supplier_invoice_id' => $supplierInvoice->id,
        ]);

        $this->logStep('Supplier invoice created', [
            'document_id' => $document->id,
            'supplier_invoice_id' => $supplierInvoice->id,
            'supplier_name' => $supplierName,
            'supplier_ruc' => $supplierRuc,
        ]);
    }

    /**
     * Find or create a supplier by RUC or name
     */
    private function findOrCreateSupplier(int $clientId, string $supplierName, ?string $supplierRuc = null, ?string $supplierDv = null): Supplier
    {
        // Try to find by RUC first (more reliable unique identifier)
        if ($supplierRuc) {
            $supplier = Supplier::where('client_id', $clientId)
                ->where('ruc', $supplierRuc)
                ->first();

            if ($supplier) {
                $this->logStep('Found existing supplier by RUC', [
                    'client_id' => $clientId,
                    'supplier_id' => $supplier->id,
                    'supplier_ruc' => $supplierRuc,
                ]);

                return $supplier;
            }
        }

        // Fall back to finding by name
        $supplier = Supplier::where('client_id', $clientId)
            ->where('legal_name', $supplierName)
            ->first();

        if ($supplier) {
            $this->logStep('Found existing supplier by name', [
                'client_id' => $clientId,
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplierName,
            ]);

            return $supplier;
        }

        $this->logStep('Creating new supplier', [
            'client_id' => $clientId,
            'supplier_name' => $supplierName,
            'supplier_ruc' => $supplierRuc,
            'supplier_dv' => $supplierDv,
        ]);

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
        array $extractedData
    ): SupplierInvoice {
        $invoiceNumber = $extractedData['bill_number'] ?? 'INV-'.$document->id;
        $invoiceDate = $this->parseDate($extractedData['issue_date'] ?? now());
        $dueDate = $this->parseDate($extractedData['due_date'] ?? now()->addDays(30));
        $subtotal = (float) ($extractedData['subtotal_amount'] ?? 0);
        $taxAmount = (float) ($extractedData['itbms_amount'] ?? 0);
        $totalAmount = (float) ($extractedData['total_amount'] ?? $subtotal + $taxAmount);

        return SupplierInvoice::create([
            'uuid' => Str::uuid(),
            'client_id' => $document->client_id,
            'supplier_id' => $supplier->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'received_date' => now()->toDateString(),
            'due_date' => $dueDate,
            'currency' => 'PAB',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'balance' => $totalAmount,
            'status' => 'registered',
            'document_path' => $document->file_path,
            'document_filename' => $document->original_filename,
            'created_by' => auth()->id() ?? 1,
            'updated_by' => auth()->id() ?? 1,
        ]);
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        }

        if (is_string($date)) {
            try {
                return Carbon::parse($date);
            } catch (\Exception $e) {
                $this->logError('Failed to parse date', ['date' => $date, 'error' => $e->getMessage()]);

                return now();
            }
        }

        return now();
    }
}
