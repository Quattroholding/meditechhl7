<?php

namespace App\Services\DocumentProcessors;

use Illuminate\Support\Facades\Log;

abstract class BaseDocumentProcessor implements DocumentProcessorInterface
{
    /**
     * Log processing step
     */
    protected function logStep(string $message, array $context = []): void
    {
        Log::info("DocumentProcessor: {$message}", $context);
    }

    /**
     * Log processing error
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error("DocumentProcessor Error: {$message}", $context);
    }
}
