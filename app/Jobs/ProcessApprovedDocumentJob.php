<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\DocumentUpload;
use App\Services\DocumentProcessors\DiscardDocumentProcessor;
use App\Services\DocumentProcessors\InventoryDocumentProcessor;
use App\Services\DocumentProcessors\RegisterElectricityBillProcessor;
use App\Services\DocumentProcessors\RegisterGasBillProcessor;
use App\Services\DocumentProcessors\RegisterWaterBillProcessor;
use App\Services\DocumentProcessors\SaveReferenceDocumentProcessor;
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

            // Get processor - prioritize action from approval if available
            $approval = $document->approval;
            $action = $approval?->selected_action;
            $processor = $this->getProcessor($document, $action);

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
     * Get processor based on document type and selected action
     */
    private function getProcessor(DocumentUpload $document, ?string $action)
    {
        // If a specific action was selected by the user, use that processor
        if ($action) {
            return match ($action) {
                'register_electricity_bill' => new RegisterElectricityBillProcessor,
                'register_water_bill' => new RegisterWaterBillProcessor,
                'register_gas_bill' => new RegisterGasBillProcessor,
                'save_for_reference' => new SaveReferenceDocumentProcessor,
                'discard' => new DiscardDocumentProcessor,
                default => $this->getDefaultProcessor($document->document_type),
            };
        }

        // Otherwise, use the default processor for the document type
        return $this->getDefaultProcessor($document->document_type);
    }

    /**
     * Get default processor based on document type
     */
    private function getDefaultProcessor(DocumentType $type)
    {
        return match ($type) {
            DocumentType::INVENTORY => new InventoryDocumentProcessor,
            DocumentType::ENSA => new SaveReferenceDocumentProcessor,
            DocumentType::NATURGY => new SaveReferenceDocumentProcessor,
            DocumentType::IDAAN => new SaveReferenceDocumentProcessor,
            DocumentType::OTRO => new SaveReferenceDocumentProcessor,
        };
    }
}
