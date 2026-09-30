<?php

namespace App\Services\DocumentProcessors;

use App\Models\DocumentUpload;
use Illuminate\Support\Facades\DB;

class RegisterElectricityBillProcessor extends BaseDocumentProcessor
{
    /**
     * Process document by registering it as an electricity bill
     *
     * Extracts generic data from the parsed result and creates a
     * utility_bills record with bill_type = 'electricity'
     */
    public function process(DocumentUpload $document): bool
    {
        try {
            $this->logStep('Processing electricity bill', [
                'document_id' => $document->id,
            ]);

            // Get extracted data
            $parseResult = $document->parseResult;
            if (! $parseResult) {
                throw new \Exception('No parse result found');
            }

            $extractedData = $parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }

            // Extract fields for utility bill
            $billData = $this->mapToBillData($extractedData, 'electricity');

            // Create utility bill record
            DB::table('utility_bills')->insert([
                'document_upload_id' => $document->id,
                'client_id' => $document->client_id,
                'bill_type' => 'electricity',
                'bill_number' => $billData['bill_number'] ?? null,
                'customer_name' => $billData['customer_name'] ?? null,
                'service_address' => $billData['service_address'] ?? null,
                'billing_period_start' => $billData['billing_period_start'] ?? null,
                'billing_period_end' => $billData['billing_period_end'] ?? null,
                'consumption_kwh' => $billData['consumption_kwh'] ?? null,
                'consumption_value' => $billData['consumption_value'] ?? null,
                'total_amount' => $billData['total_amount'] ?? null,
                'metadata' => json_encode($billData['metadata'] ?? []),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->logStep('Electricity bill registered successfully', [
                'document_id' => $document->id,
                'bill_data' => $billData,
            ]);

            $document->markAsProcessed();

            return true;

        } catch (\Exception $e) {
            $this->logError('Failed to register electricity bill', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Map extracted data to bill structure
     */
    private function mapToBillData(array $extractedData, string $billType): array
    {
        $billData = [
            'bill_number' => null,
            'customer_name' => null,
            'service_address' => null,
            'billing_period_start' => null,
            'billing_period_end' => null,
            'consumption_kwh' => null,
            'consumption_value' => null,
            'total_amount' => null,
            'metadata' => [],
        ];

        // For electricity bills, look for common field names
        $keyValuePairs = $extractedData['key_value_pairs'] ?? [];

        foreach ($keyValuePairs as $key => $value) {
            $lowerKey = strtolower($key);

            if (strpos($lowerKey, 'factura') !== false || strpos($lowerKey, 'bill') !== false || strpos($lowerKey, 'número') !== false) {
                $billData['bill_number'] = $value;
            } elseif (strpos($lowerKey, 'cliente') !== false || strpos($lowerKey, 'customer') !== false) {
                $billData['customer_name'] = $value;
            } elseif (strpos($lowerKey, 'dirección') !== false || strpos($lowerKey, 'address') !== false) {
                $billData['service_address'] = $value;
            } elseif (strpos($lowerKey, 'consumo') !== false && strpos($lowerKey, 'kwh') !== false) {
                $billData['consumption_kwh'] = $this->extractNumber($value);
            } elseif (strpos($lowerKey, 'total') !== false) {
                $billData['total_amount'] = $this->extractNumber($value);
            }
        }

        // Look for numeric fields
        $numericFields = $extractedData['numeric_fields'] ?? [];
        foreach ($numericFields as $field) {
            $lowerLabel = strtolower($field['label'] ?? '');
            if (strpos($lowerLabel, 'kwh') !== false && empty($billData['consumption_kwh'])) {
                $billData['consumption_kwh'] = $field['value'] ?? null;
            } elseif (strpos($lowerLabel, 'total') !== false && empty($billData['total_amount'])) {
                $billData['total_amount'] = $field['value'] ?? null;
            }
        }

        // Store original extracted data as metadata
        $billData['metadata'] = $extractedData;

        return $billData;
    }

    /**
     * Extract numeric value from string
     */
    private function extractNumber(string $value): ?float
    {
        // Remove currency symbols and spaces
        $cleaned = preg_replace('/[^\d.,\-]/', '', $value);

        // Replace comma with dot for decimals
        $cleaned = str_replace(',', '.', $cleaned);

        // Handle multiple dots
        if (substr_count($cleaned, '.') > 1) {
            $parts = explode('.', $cleaned);
            $decimal = array_pop($parts);
            $integer = implode('', $parts);
            $cleaned = $integer.'.'.$decimal;
        }

        $number = (float) $cleaned;

        return $number !== 0.0 ? $number : null;
    }
}
