<?php

namespace App\Jobs;

use App\Enums\DocumentType;
use App\Models\DocumentUpload;
use App\Services\DocumentParsers\AnthropicDocumentParser;
use App\Services\DocumentParsers\InventoryAiDocumentParser;
use App\Services\DocumentParsers\InventoryDocumentParser;
use App\Services\GoogleDocumentAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

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

            // For inventory_ai documents, try pdfparser first (better table extraction)
            // For other types, use Google OCR
            if ($document->document_type === DocumentType::INVENTORY_AI) {
                $googleAIResponse = $this->extractWithPdfParser($document);
                if (empty($googleAIResponse['document']['text'])) {
                    // If pdfparser fails, fallback to Google OCR
                    Log::info('ParseDocumentJob: pdfparser extraction empty, falling back to Google OCR', [
                        'document_id' => $document->id,
                    ]);
                    $googleAIResponse = $this->extractWithGoogleDocumentAI($document);
                }
            } else {
                // Use Google Document AI for other types
                $googleAIResponse = $this->extractWithGoogleDocumentAI($document);
            }

            // Get parser for document type
            $parser = $this->getParser($document->document_type);
            $parseResult = $parser->parse($googleAIResponse);

            // Save parsing result
            $document->parseResult()->delete(); // Delete any previous result

            // Build extracted data array - support both structured (inventory) and generic data
            // Start with all parser results, then add overrides for consistency
            $extractedDataArray = array_merge(
                // Copy all parser results
                $parseResult,
                // Explicit overrides for standard fields
                [
                    'items' => $parseResult['items'] ?? [],
                    'confidence' => $parseResult['confidence'],
                    'subtotal' => $parseResult['subtotal'] ?? 0,
                    'total_tax' => $parseResult['total_tax'] ?? 0,
                    'total' => $parseResult['total'] ?? 0,
                ]
            );

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
     * Extract text from PDF using pdfparser library
     * Better for table extraction than Google OCR
     */
    private function extractWithPdfParser(DocumentUpload $document): array
    {
        try {
            $diskName = config('filesystems.default', 'local');
            $filePath = Storage::disk($diskName)->path($document->file_path);

            $parser = new PdfParser;
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            Log::info('ParseDocumentJob: Extracted text with pdfparser', [
                'document_id' => $document->id,
                'text_length' => strlen($text),
            ]);

            return [
                'document' => [
                    'mime_type' => 'application/pdf',
                    'text' => $text,
                    'pages' => [],
                ],
            ];
        } catch (\Exception $e) {
            Log::warning('ParseDocumentJob: pdfparser extraction failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return ['document' => ['text' => '', 'mime_type' => 'application/pdf']];
        }
    }

    /**
     * Extract text using Google Document AI service
     */
    private function extractWithGoogleDocumentAI(DocumentUpload $document): array
    {
        try {
            if (! GoogleDocumentAIService::isConfigured()) {
                throw new \RuntimeException('Google Document AI is not configured');
            }

            $service = new GoogleDocumentAIService;
            $diskName = config('filesystems.default', 'local');

            return $service->parseDocument($document->file_path, $diskName);
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();

            // Check if it's an entity_types configuration error
            if (str_contains($errorMsg, 'entity_types') || str_contains($errorMsg, 'INVALID_ARGUMENT')) {
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

            return ['document' => ['text' => '', 'mime_type' => 'application/pdf']];
        }
    }

    /**
     * Get parser based on document type
     */
    private function getParser(DocumentType $type)
    {
        return match ($type) {
            DocumentType::INVENTORY => new InventoryDocumentParser,
            DocumentType::INVENTORY_AI => new InventoryAiDocumentParser,
            DocumentType::ENSA => new AnthropicDocumentParser,
            DocumentType::IDAAN => new AnthropicDocumentParser,
            DocumentType::NATURGY => new AnthropicDocumentParser,
            default => new AnthropicDocumentParser,
        };

    }
}
