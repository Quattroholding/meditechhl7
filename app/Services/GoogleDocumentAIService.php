<?php

namespace App\Services;

use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Google\Cloud\DocumentAI\V1\RawDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleDocumentAIService
{
    private DocumentProcessorServiceClient $client;

    private string $projectId;

    private string $location;

    private string $processorId;

    public function __construct()
    {
        if (! self::isConfigured()) {
            throw new \RuntimeException('Google Document AI is not enabled');
        }

        $this->projectId = config('services.google.document_ai.project_id');
        $this->location = config('services.google.document_ai.location');
        $this->processorId = config('services.google.document_ai.processor_id');

        if (! $this->projectId || ! $this->processorId) {
            throw new \RuntimeException('Google Document AI configuration is missing (project_id or processor_id)');
        }

        $this->initializeClient();
    }

    /**
     * Initialize Google Cloud Document AI client
     */
    private function initializeClient(): void
    {
        try {
            // The credentials are handled via GOOGLE_APPLICATION_CREDENTIALS env var
            $this->client = new DocumentProcessorServiceClient;
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Document AI client', [
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to initialize Google Document AI client: '.$e->getMessage());
        }
    }

    /**
     * Parse a PDF document using Google Document AI
     *
     * @param  string  $filePath  Path to the PDF file in storage
     * @param  string  $diskName  Disk name where file is stored (default: 'local')
     * @return array Parsed document response from Google AI
     *
     * @throws \Exception
     */
    public function parseDocument(string $filePath, string $diskName = 'local'): array
    {
        try {
            // Read the PDF file from the specified disk
            $disk = Storage::disk($diskName);

            if (! $disk->exists($filePath)) {
                Log::error('File not found in disk', [
                    'disk' => $diskName,
                    'path' => $filePath,
                    'exists_in_local' => Storage::disk('local')->exists($filePath),
                    'exists_in_public' => Storage::disk('public')->exists($filePath),
                ]);
                throw new \RuntimeException("File not found in disk '{$diskName}': {$filePath}");
            }

            $fileContent = $disk->get($filePath);
            $mimeType = 'application/pdf';

            // Prepare the request
            $rawDocument = new RawDocument([
                'content' => $fileContent,
                'mime_type' => $mimeType,
            ]);

            $processorName = "projects/{$this->projectId}/locations/{$this->location}/processors/{$this->processorId}";

            $request = new ProcessRequest([
                'name' => $processorName,
                'raw_document' => $rawDocument,
            ]);

            Log::info('Sending document to Google Document AI', [
                'processor' => $processorName,
                'file' => basename($filePath),
                'size' => strlen($fileContent),
            ]);

            // Call the API
            $response = $this->client->processDocument($request);

            Log::info('Document processed successfully', [
                'processor' => $processorName,
                'document_type' => $response->getDocument()?->getMimeType(),
            ]);

            // Convert to array for easier handling
            return $this->responseToArray($response);

        } catch (\Exception $e) {
            Log::error('Error processing document with Google Document AI', [
                'error' => $e->getMessage(),
                'file' => $filePath,
                'code' => $e->getCode(),
            ]);
            throw $e;
        }
    }

    /**
     * Convert API response to array
     * Returns wrapped structure: ['document' => [...google response...]]
     */
    private function responseToArray($response): array
    {
        try {
            // Get the document object
            $document = $response->getDocument();

            if (! $document) {
                return ['document' => []];
            }

            // Convert protobuf to JSON to array
            // serializeToJsonString() already returns a JSON string, don't double-encode
            $jsonString = $document->serializeToJsonString();
            $documentData = json_decode($jsonString, true) ?? [];

            // Wrap in 'document' key to maintain consistent structure for parsers
            return ['document' => $documentData];

        } catch (\Exception $e) {
            Log::error('Error converting Google Document AI response', [
                'error' => $e->getMessage(),
            ]);

            return ['document' => []];
        }
    }

    /**
     * Validate configuration
     */
    public static function isConfigured(): bool
    {
        $enabled = config('services.google.document_ai.enabled', false);
        $projectId = config('services.google.document_ai.project_id');
        $processorId = config('services.google.document_ai.processor_id');

        return (bool) ($enabled && $projectId && $processorId);
    }
}
