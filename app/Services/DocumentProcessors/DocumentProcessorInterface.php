<?php

namespace App\Services\DocumentProcessors;

use App\Models\DocumentUpload;

interface DocumentProcessorInterface
{
    /**
     * Process an approved document and create related records.
     *
     * @param  DocumentUpload  $document  The approved document to process
     * @return bool True if processing was successful
     *
     * @throws \Exception If processing fails
     */
    public function process(DocumentUpload $document): bool;
}
