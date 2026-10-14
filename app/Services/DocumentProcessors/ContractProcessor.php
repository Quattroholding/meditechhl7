<?php

namespace App\Services\DocumentProcessors;

use App\Enums\SupplierInvoiceStatus;
use App\Jobs\GeneratePeriodicContractInvoiceJob;
use App\Models\DocumentUpload;
use App\Models\Finance\Supplier;
use App\Models\Finance\SupplierInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContractProcessor extends BaseDocumentProcessor
{
    /**
     * Process an approved contract document
     * Creates a supplier and generates periodic invoices based on payment frequency
     */
    public function process(DocumentUpload $document): bool
    {
        try {
            $this->logStep('Starting contract document processing', [
                'document_id' => $document->id,
                'client_id' => $document->client_id,
            ]);

            // Validate accounting is enabled for this client
            if (! $this->shouldProcess($document)) {
                $this->logStep('Contract processing skipped: accounting not enabled', [
                    'document_id' => $document->id,
                    'client_id' => $document->client_id,
                ]);

                return false;
            }

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

            $this->logStep('Extracted contract data', [
                'contract_type' => $extractedData['contract_type'] ?? 'N/A',
                'party_1_name' => $extractedData['party_1_name'] ?? 'N/A',
                'contract_value' => $extractedData['contract_value'] ?? 0,
                'payment_frequency' => $extractedData['payment_frequency'] ?? 'N/A',
                'start_date' => $extractedData['start_date'] ?? 'N/A',
                'end_date' => $extractedData['end_date'] ?? 'N/A',
            ]);

            // Process within a transaction for atomicity
            DB::transaction(function () use ($document, $extractedData) {
                // Validate required contract fields
                $contractData = $this->validateContractData($extractedData);

                // Create or get supplier (party_1 is the service provider/landlord who receives payment)
                $supplier = $this->findOrCreateSupplier($document->client_id, $contractData);

                // Generate and create invoices based on payment frequency
                $invoices = $this->generateInvoices($document, $supplier, $contractData);

                $this->logStep('Contract invoices created', [
                    'document_id' => $document->id,
                    'invoice_count' => count($invoices),
                    'supplier_id' => $supplier->id,
                ]);

                // Update document with reference and keep in approved status
                $document->update([
                    'supplier_id' => $supplier->id,
                    'notes' => 'Contract ID: '.$extractedData['contract_number'] ?? 'N/A',
                    'status' => 'approved', // Keep in approved status for re-processing if needed
                ]);
            });

            $this->logStep('Contract document processed successfully', [
                'document_id' => $document->id,
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logError('Contract processing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Validate and extract required contract data
     */
    private function validateContractData(array $extractedData): array
    {
        $required = ['party_1_name', 'contract_value', 'payment_frequency', 'start_date', 'end_date'];

        foreach ($required as $field) {
            if (empty($extractedData[$field])) {
                throw new \RuntimeException("Contract field '{$field}' is required");
            }
        }

        return [
            'contract_number' => $extractedData['contract_number'] ?? null,
            'contract_title' => $extractedData['contract_title'] ?? null,
            'contract_type' => $extractedData['contract_type'] ?? null,
            'party_1_name' => $extractedData['party_1_name'],
            'party_1_type' => $extractedData['party_1_type'] ?? 'individual',
            'party_1_id' => $extractedData['party_1_id'] ?? null,
            'contract_value' => (float) $extractedData['contract_value'],
            'currency' => $extractedData['currency'] ?? 'PAB',
            'payment_frequency' => $extractedData['payment_frequency'],
            'payment_amount' => (float) ($extractedData['payment_amount'] ?? $extractedData['contract_value']),
            'start_date' => $extractedData['start_date'],
            'end_date' => $extractedData['end_date'],
            'property_address' => $extractedData['property_address'] ?? null,
        ];
    }

    /**
     * Find or create a supplier based on contract party_1 (landlord/service provider)
     */
    private function findOrCreateSupplier(int $clientId, array $contractData): Supplier
    {
        $supplierName = $contractData['party_1_name'];
        $supplierRuc = $contractData['party_1_id'];

        // Try to find by name
        $supplier = Supplier::where('client_id', $clientId)
            ->where('legal_name', $supplierName)
            ->first();

        if ($supplier) {
            $this->logStep('Found existing supplier', [
                'client_id' => $clientId,
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplierName,
            ]);

            return $supplier;
        }

        // Create new supplier
        $this->logStep('Creating new supplier from contract', [
            'client_id' => $clientId,
            'supplier_name' => $supplierName,
            'supplier_id' => $supplierRuc,
        ]);

        // Get the default CxP account
        $cxpAccount = DB::table('accounting_accounts')
            ->where('client_id', $clientId)
            ->where('code', '2101')
            ->first();

        $accountingAccountId = $cxpAccount?->id;

        if (! $accountingAccountId) {
            $anyPayable = DB::table('accounting_accounts')
                ->where('client_id', $clientId)
                ->where('account_type', 'liability')
                ->first();

            if ($anyPayable) {
                $accountingAccountId = $anyPayable->id;
            }
        }

        try {
            // Generate a default RUC if not provided (short format for contracts)
            // Use timestamp + random numbers to create a unique 12-digit RUC
            $ruc = $supplierRuc ?? str_pad((int) (microtime(true) * 1000) % 1000000000000, 12, '0', STR_PAD_LEFT);

            $supplier = Supplier::create([
                'uuid' => Str::uuid(),
                'client_id' => $clientId,
                'ruc' => $ruc,
                'legal_name' => $supplierName,
                'commercial_name' => $supplierName,
                'address' => $contractData['property_address'] ?? null,
                'accounting_account_id' => $accountingAccountId,
                'status' => 'active',
                'created_by' => auth()->id() ?? 1,
                'updated_by' => auth()->id() ?? 1,
            ]);

            $this->logStep('Supplier created successfully', [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplierName,
            ]);

            return $supplier;
        } catch (\Exception $e) {
            $this->logError('Failed to create supplier', [
                'supplier_name' => $supplierName,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate first invoice and schedule remaining periodic invoices
     * Creates initial invoice and dispatches jobs for future invoices
     *
     * @return SupplierInvoice[]
     */
    private function generateInvoices(DocumentUpload $document, Supplier $supplier, array $contractData): array
    {
        $invoices = [];
        $startDate = Carbon::parse($contractData['start_date']);
        $endDate = Carbon::parse($contractData['end_date']);
        $frequency = $contractData['payment_frequency'];
        $paymentAmount = $contractData['payment_amount'];
        $currency = $contractData['currency'];

        // Create first invoice immediately
        $currentDate = $startDate->copy();
        $invoiceNumber = 1;

        $invoice = SupplierInvoice::create([
            'uuid' => Str::uuid(),
            'client_id' => $document->client_id,
            'supplier_id' => $supplier->id,
            'invoice_number' => $supplier->id.'-'.$document->id.'-'.str_pad($invoiceNumber, 4, '0', STR_PAD_LEFT),
            'invoice_date' => $currentDate->toDateString(),
            'received_date' => $currentDate->toDateString(),
            'due_date' => $currentDate->copy()->addDay()->toDateString(),
            'currency' => $currency,
            'subtotal' => $paymentAmount,
            'tax_amount' => 0,
            'total_amount' => $paymentAmount,
            'paid_amount' => 0,
            'balance' => $paymentAmount,
            'status' => SupplierInvoiceStatus::REGISTERED->value,
            'document_path' => $document->file_path,
            'document_filename' => $document->original_filename,
            'notes' => $contractData['contract_title'] ?? 'Contract Invoice',
            'created_by' => auth()->id() ?? 1,
            'updated_by' => auth()->id() ?? 1,
        ]);

        // Load client relation before approving (Observer needs it)
        $invoice->load('client');

        // Approve the invoice to trigger Observer and generate journal entry
        $invoice->update([
            'status' => SupplierInvoiceStatus::APPROVED->value,
            'approved_at' => now(),
            'approved_by' => auth()->id() ?? 1,
        ]);

        $invoices[] = $invoice;

        $this->logStep('First invoice created', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'invoice_date' => $currentDate->toDateString(),
        ]);

        // Schedule remaining invoices as jobs with delays
        $currentDate = $this->getNextPeriodDate($currentDate, $frequency);
        $invoiceNumber = 2;
        $nowTime = now();

        $this->logStep('Starting job scheduling', [
            'now' => $nowTime->toDateTimeString(),
            'start_scheduling_date' => $currentDate->toDateTimeString(),
            'end_date' => $endDate->toDateTimeString(),
        ]);

        while ($currentDate <= $endDate) {
            // Calculate delay as direct timestamp difference
            // Positive = future, negative/zero = past or current
            $delaySeconds = (int) ($currentDate->timestamp - $nowTime->timestamp);

            $this->logStep('Evaluating invoice date', [
                'sequence' => $invoiceNumber,
                'invoice_date' => $currentDate->toDateTimeString(),
                'now' => $nowTime->toDateTimeString(),
                'currentDate_timestamp' => $currentDate->timestamp,
                'nowTime_timestamp' => $nowTime->timestamp,
                'delay_seconds' => $delaySeconds,
                'delay_positive' => $delaySeconds > 0,
            ]);

            // Schedule jobs only for future dates
            if ($delaySeconds > 0) {
                GeneratePeriodicContractInvoiceJob::dispatch(
                    $document->id,
                    $supplier->id,
                    $invoiceNumber,
                    $currentDate,
                    $contractData,
                )->delay($delaySeconds);

                $this->logStep('✓ Scheduled invoice job', [
                    'sequence' => $invoiceNumber,
                    'invoice_date' => $currentDate->toDateString(),
                    'delay_seconds' => $delaySeconds,
                    'delay_days' => $delaySeconds / 86400,
                ]);
            } else {
                $this->logStep('⊗ Skipped invoice date (past or same)', [
                    'sequence' => $invoiceNumber,
                    'invoice_date' => $currentDate->toDateString(),
                    'delay_seconds' => $delaySeconds,
                ]);
            }

            // Move to next period
            $currentDate = $this->getNextPeriodDate($currentDate, $frequency);
            $invoiceNumber++;
        }

        return $invoices;
    }

    /**
     * Calculate next period date based on frequency
     */
    private function getNextPeriodDate(Carbon $currentDate, string $frequency): Carbon
    {
        return match ($frequency) {
            'weekly' => $currentDate->copy()->addWeek(),
            'monthly' => $currentDate->copy()->addMonth(),
            'yearly', 'annual' => $currentDate->copy()->addYear(),
            default => $currentDate->copy()->addMonth(),
        };
    }

    /**
     * Validate if contract processing should proceed
     * Checks if accounting is enabled for the client
     */
    private function shouldProcess(DocumentUpload $document): bool
    {
        // Load client relation if not already loaded
        if (! $document->relationLoaded('client')) {
            $document->load('client');
        }

        // Contract processing requires accounting to be enabled
        if (! $document->client?->accounting_enabled) {
            return false;
        }

        return true;
    }
}
