<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\DocumentUpload;
use App\Services\DocumentProcessors\InventoryDocumentProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessApprovedDocumentJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 300; // 5 minutes

    public int $tries = 3;

    public array $backoff = [60, 300, 600];

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
            Log::error('ProcessApprovedDocumentJob: Document not found', ['id' => $this->documentUploadId]);

            return;
        }

        try {
            // Verify document is approved
            if ($document->status !== DocumentStatus::APPROVED) {
                Log::warning('ProcessApprovedDocumentJob: Document is not approved', [
                    'document_id' => $document->id,
                    'status' => $document->status->value,
                ]);

                return;
            }

            Log::info('ProcessApprovedDocumentJob: Starting processing', [
                'document_id' => $document->id,
                'type' => $document->document_type->value,
            ]);

            // Mark as processing
            $document->markAsProcessing();

            // Get processor for document type
            $processor = $this->getProcessor($document->document_type);

            // Process document
            $processor->process($document);

            Log::info('ProcessApprovedDocumentJob: Processing completed successfully', [
                'document_id' => $document->id,
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessApprovedDocumentJob: Error during processing', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Mark as processing failed
            $document->markAsProcessingFailed($e->getMessage());

            // Throw only if last attempt
            if ($this->attempts() >= $this->tries) {
                throw $e;
            }
        }
    }

    /**
     * Get processor based on document type
     */
    private function getProcessor(DocumentType $type)
    {
        return match ($type) {
            DocumentType::INVENTORY => new InventoryDocumentProcessor,
            DocumentType::ELECTRICITY_BILL => throw new \RuntimeException('ElectricityBillProcessor not yet implemented'),
            DocumentType::WATER_BILL => throw new \RuntimeException('WaterBillProcessor not yet implemented'),
            DocumentType::GAS_BILL => throw new \RuntimeException('GasBillProcessor not yet implemented'),
        };
    }
}
