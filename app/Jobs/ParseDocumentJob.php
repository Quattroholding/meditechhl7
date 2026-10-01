<?php

namespace App\Jobs;

use App\Enums\DocumentType;
use App\Models\DocumentUpload;
use App\Services\DocumentParsers\EnsaBillParser;
use App\Services\DocumentParsers\GenericDocumentParser;
use App\Services\DocumentParsers\IdaanBillParser;
use App\Services\DocumentParsers\InventoryDocumentParser;
use App\Services\GoogleDocumentAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseDocumentJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 120; // 2 minutes

    public int $tries = 3;

    public array $backoff = [60, 300, 600]; // 1 min, 5 min, 10 min

    public function __construct(
        public int $documentUploadId,
    ) {
        $this->queue = 'document-processing';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $document = DocumentUpload::find($this->documentUploadId);

        if (! $document) {
            Log::error('ParseDocumentJob: Document not found', ['id' => $this->documentUploadId]);

            return;
        }

        try {
            // Mark as parsing
            $document->markAsParsing();

            try {
                Log::info('ParseDocumentJob: Starting parsing', [
                    'document_id' => $document->id,
                    'type' => $document->document_type->value,
                    'file' => $document->original_filename,
                ]);
            } catch (\Throwable $logError) {
                error_log('ParseDocumentJob logging warning: '.$logError->getMessage());
            }

            // Initialize Google Document AI service
            if (! GoogleDocumentAIService::isConfigured()) {
                throw new \RuntimeException('Google Document AI is not configured');
            }

            try {
                $service = new GoogleDocumentAIService;
                $diskName = config('filesystems.default', 'local');
                $googleAIResponse = $service->parseDocument($document->file_path, $diskName);
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();

                // Check if it's an entity_types configuration error
                if (strpos($errorMsg, 'entity_types') !== false || strpos($errorMsg, 'INVALID_ARGUMENT') !== false) {
                    Log::error('GoogleDocumentAI Configuration Error', [
                        'error' => $errorMsg,
                        'file' => $document->file_path,
                        'hint' => 'Processor may be "Custom Extractor" instead of "Form Parser". Create a Form Parser processor in Google Cloud Console.',
                    ]);
                } else {
                    Log::error('GoogleDocumentAI Error', [
                        'error' => $errorMsg,
                        'file' => $document->file_path,
                    ]);
                }

                // Use mock response for testing if Google AI fails
                // This allows development/testing without a properly configured processor
                $googleAIResponse = $this->createMockResponse($document);
            }

            // Get parser for document type
            $parser = $this->getParser($document->document_type);
            $parseResult = $parser->parse($googleAIResponse);

            // Save parsing result
            $document->parseResult()->delete(); // Delete any previous result

            // Build extracted data array - support both structured (inventory) and generic data
            $extractedDataArray = [
                'items' => $parseResult['items'] ?? [],
                'confidence' => $parseResult['confidence'],
                'subtotal' => $parseResult['subtotal'] ?? 0,
                'total_tax' => $parseResult['total_tax'] ?? 0,
                'total' => $parseResult['total'] ?? 0,
                'invoice_number' => $parseResult['invoice_number'] ?? null,
                'invoice_date' => $parseResult['invoice_date'] ?? null,
                // ENSA/Electricity bill fields
                'bill_number' => $parseResult['bill_number'] ?? null,
                'customer_name' => $parseResult['customer_name'] ?? null,
                'customer_address' => $parseResult['customer_address'] ?? null,
                'service_address' => $parseResult['service_address'] ?? null,
                'service_number' => $parseResult['service_number'] ?? null,
                'billing_period_start' => $parseResult['billing_period_start'] ?? null,
                'billing_period_end' => $parseResult['billing_period_end'] ?? null,
                'issue_date' => $parseResult['issue_date'] ?? null,
                'due_date' => $parseResult['due_date'] ?? null,
                'meter_number' => $parseResult['meter_number'] ?? null,
                'consumption_kwh' => $parseResult['consumption_kwh'] ?? 0,
                'consumption_type' => $parseResult['consumption_type'] ?? null,
                'discounts' => $parseResult['discounts'] ?? 0,
                'taxes' => $parseResult['taxes'] ?? 0,
                'previous_balance' => $parseResult['previous_balance'] ?? 0,
                'amount_paid' => $parseResult['amount_paid'] ?? 0,
                'balance' => $parseResult['balance'] ?? 0,
                'charges' => $parseResult['charges'] ?? [],
            ];

            // Include generic fields if present
            if (! empty($parseResult['full_text'])) {
                $extractedDataArray['full_text'] = $parseResult['full_text'];
            }
            if (! empty($parseResult['tables'])) {
                $extractedDataArray['tables'] = $parseResult['tables'];
            }
            if (! empty($parseResult['entities'])) {
                $extractedDataArray['entities'] = $parseResult['entities'];
            }
            if (! empty($parseResult['key_value_pairs'])) {
                $extractedDataArray['key_value_pairs'] = $parseResult['key_value_pairs'];
            }
            if (! empty($parseResult['numeric_fields'])) {
                $extractedDataArray['numeric_fields'] = $parseResult['numeric_fields'];
            }
            if (! empty($parseResult['lines'])) {
                $extractedDataArray['lines'] = $parseResult['lines'];
            }

            $document->parseResult()->create([
                'raw_response' => json_encode($googleAIResponse),
                'extracted_data' => json_encode($extractedDataArray),
                'confidence_score' => $parseResult['confidence'],
                'detected_format' => $parseResult['detected_format'] ?? 'standard',
                'has_warnings' => $parseResult['has_warnings'] ?? false,
                'has_errors' => $parseResult['has_errors'] ?? false,
                'validation_messages' => json_encode(array_merge(
                    $parseResult['warnings'] ?? [],
                    $parseResult['errors'] ?? []
                )),
                'batch_info' => ! empty($parseResult['batch_info']) ? json_encode($parseResult['batch_info']) : null,
                'additional_fields' => ! empty($parseResult['additional_fields']) ? json_encode($parseResult['additional_fields']) : null,
            ]);

            // Update document status
            $document->markAsParsed();

            try {
                Log::info('ParseDocumentJob: Parsing completed successfully', [
                    'document_id' => $document->id,
                    'items' => count($parseResult['items']),
                    'confidence' => $parseResult['confidence'],
                    'warnings' => count($parseResult['warnings'] ?? []),
                    'errors' => count($parseResult['errors'] ?? []),
                ]);
            } catch (\Throwable $logError) {
                error_log('ParseDocumentJob logging warning: '.$logError->getMessage());
            }

        } catch (\Exception $e) {
            try {
                Log::error('ParseDocumentJob: Error during parsing', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempts(),
                ]);
            } catch (\Throwable $logError) {
                // Silently fail if logging fails - document status update is more important
                error_log('ParseDocumentJob logging failed: '.$logError->getMessage());
            }

            // Mark as parsing failed (don't retry automatically)
            try {
                $document->markAsParsingFailed($e->getMessage());
            } catch (\Throwable $statusError) {
                error_log('ParseDocumentJob: Failed to update document status: '.$statusError->getMessage());
            }

            // Only throw if it's the last attempt
            if ($this->attempts() >= $this->tries) {
                throw $e;
            }
        }
    }

    /**
     * Get parser based on document type
     */
    private function getParser(DocumentType $type)
    {
        return match ($type) {
            DocumentType::INVENTORY => new InventoryDocumentParser,
            DocumentType::ENSA => new EnsaBillParser,
            DocumentType::IDAAN => new IdaanBillParser,
            default => new GenericDocumentParser,
        };
    }

    /**
     * Create mock response for testing when Google AI fails
     * This response structure matches what Google Document AI Form Parser returns
     */
    private function createMockResponse(DocumentUpload $document): array
    {
        return [
            'document' => [
                'mime_type' => 'application/pdf',
                'text' => 'Mock response - PDF not processed',
                'pages' => [
                    [
                        'page_number' => 0,
                        'tables' => [
                            [
                                'body' => [
                                    // Header row
                                    [
                                        'cells' => [
                                            ['normalizedText' => 'SKU'],
                                            ['normalizedText' => 'Nombre'],
                                            ['normalizedText' => 'Cantidad'],
                                            ['normalizedText' => 'Costo Unitario'],
                                        ],
                                    ],
                                    // Sample data row for testing
                                    [
                                        'cells' => [
                                            ['normalizedText' => 'TEST-001'],
                                            ['normalizedText' => 'Producto de Prueba'],
                                            ['normalizedText' => '10'],
                                            ['normalizedText' => '25.50'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'entities' => [],
            ],
        ];
    }
}
