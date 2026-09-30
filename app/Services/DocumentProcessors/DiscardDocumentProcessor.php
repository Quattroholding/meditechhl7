<?php

namespace App\Services\DocumentProcessors;

use App\Models\DocumentUpload;

class DiscardDocumentProcessor extends BaseDocumentProcessor
{
    /**
     * Process document by discarding it
     *
     * This processor marks the document as rejected since the user
     * explicitly chose to discard it.
     */
    public function process(DocumentUpload $document): bool
    {
        try {
            $this->logStep('Document discarded', [
                'document_id' => $document->id,
                'document_type' => $document->document_type->value,
            ]);

            // Mark as rejected with discard reason
            $document->update([
                'status' => 'rejected',
                'rejection_reason' => 'Documento descartado por el usuario',
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logError('Failed to discard document', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
