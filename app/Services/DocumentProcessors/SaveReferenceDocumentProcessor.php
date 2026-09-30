<?php

namespace App\Services\DocumentProcessors;

use App\Models\DocumentUpload;

class SaveReferenceDocumentProcessor extends BaseDocumentProcessor
{
    /**
     * Process document by saving it for reference only
     *
     * This processor marks the document as processed without creating
     * any additional records in specialized tables.
     */
    public function process(DocumentUpload $document): bool
    {
        try {
            $this->logStep('Document saved for reference only', [
                'document_id' => $document->id,
                'document_type' => $document->document_type->value,
            ]);

            $document->markAsProcessed();

            return true;

        } catch (\Exception $e) {
            $this->logError('Failed to save document for reference', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
