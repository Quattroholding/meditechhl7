<?php

namespace App\Services\DocumentProcessors;

use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Log;

class ElectricityBillProcessor extends BaseDocumentProcessor
{
    /**
     * Process an approved electricity bill document
     */
    public function process(DocumentUpload $document): bool
    {
        try {
            $this->logStep('Starting electricity bill document processing', [
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

            // Store the bill information in document metadata
            $billData = [
                'bill_number' => $extractedData['bill_number'] ?? null,
                'customer_name' => $extractedData['customer_name'] ?? null,
                'service_number' => $extractedData['service_number'] ?? null,
                'meter_number' => $extractedData['meter_number'] ?? null,
                'consumption_kwh' => $extractedData['consumption_kwh'] ?? 0,
                'billing_period_start' => $extractedData['billing_period_start'] ?? null,
                'billing_period_end' => $extractedData['billing_period_end'] ?? null,
                'issue_date' => $extractedData['issue_date'] ?? null,
                'due_date' => $extractedData['due_date'] ?? null,
                'total' => $extractedData['total'] ?? 0,
                'previous_balance' => $extractedData['previous_balance'] ?? 0,
                'amount_paid' => $extractedData['amount_paid'] ?? 0,
                'balance' => $extractedData['balance'] ?? 0,
            ];

            // Log the bill data
            $this->logStep('Electricity bill data extracted', [
                'document_id' => $document->id,
                'bill_number' => $billData['bill_number'],
                'total' => $billData['total'],
                'consumption_kwh' => $billData['consumption_kwh'],
            ]);

            // Mark as processed (bills are stored for record-keeping, not inventory management)
            $document->markAsProcessed();

            $this->logStep('Electricity bill processed successfully', [
                'document_id' => $document->id,
                'bill_number' => $billData['bill_number'],
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logError('Electricity bill processing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Note: Electricity bills are informational documents
     * They don't affect inventory like purchase orders do
     * The data is stored for billing records and cost tracking
     */
}
