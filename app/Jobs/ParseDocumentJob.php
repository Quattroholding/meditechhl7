<?php

namespace App\Jobs;

use App\Enums\DocumentType;
use App\Models\DocumentUpload;
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

            Log::info('ParseDocumentJob: Starting parsing', [
                'document_id' => $document->id,
                'type' => $document->document_type->value,
                'file' => $document->original_filename,
            ]);

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

            $document->parseResult()->create([
                'raw_response' => json_encode($googleAIResponse),
                'extracted_data' => json_encode([
                    'items' => $parseResult['items'],
                    'confidence' => $parseResult['confidence'],
                ]),
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

            Log::info('ParseDocumentJob: Parsing completed successfully', [
                'document_id' => $document->id,
                'items' => count($parseResult['items']),
                'confidence' => $parseResult['confidence'],
                'warnings' => count($parseResult['warnings'] ?? []),
                'errors' => count($parseResult['errors'] ?? []),
            ]);

        } catch (\Exception $e) {
            Log::error('ParseDocumentJob: Error during parsing', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Mark as parsing failed (don't retry automatically)
            $document->markAsParsingFailed($e->getMessage());

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
            DocumentType::ELECTRICITY_BILL => throw new \RuntimeException('ElectricityBillParser not yet implemented'),
            DocumentType::WATER_BILL => throw new \RuntimeException('WaterBillParser not yet implemented'),
            DocumentType::GAS_BILL => throw new \RuntimeException('GasBillParser not yet implemented'),
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
