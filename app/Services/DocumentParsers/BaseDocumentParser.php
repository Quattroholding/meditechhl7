<?php

namespace App\Services\DocumentParsers;

use Illuminate\Support\Facades\Log;

abstract class BaseDocumentParser implements DocumentParserInterface
{
    protected array $items = [];

    protected array $warnings = [];

    protected array $errors = [];

    protected float $confidenceScore = 0.0;

    protected array $confidenceScores = [];

    /**
     * Validate a required field
     */
    protected function validateRequiredField(?string $value, string $fieldName, int $rowIndex): bool
    {
        if (empty($value)) {
            $this->addError("Fila {$rowIndex}: Campo requerido '{$fieldName}' está vacío");

            return false;
        }

        return true;
    }

    /**
     * Validate a numeric field
     */
    protected function validateNumericField($value, string $fieldName, int $rowIndex, bool $required = false): ?float
    {
        if (empty($value) && ! $required) {
            return null;
        }

        if (empty($value) && $required) {
            $this->addError("Fila {$rowIndex}: Campo requerido '{$fieldName}' está vacío");

            return null;
        }

        // Clean monetary values: remove $ symbols, spaces, and commas
        $cleaned = preg_replace('/[\$\s,]/', '', (string) $value);

        if (! is_numeric($cleaned)) {
            $this->addError("Fila {$rowIndex}: Campo '{$fieldName}' debe ser numérico, recibió: {$value}");

            return null;
        }

        return (float) $cleaned;
    }

    /**
     * Validate positive number
     */
    protected function validatePositiveNumber($value, string $fieldName, int $rowIndex, bool $required = false): ?float
    {
        $numericValue = $this->validateNumericField($value, $fieldName, $rowIndex, $required);

        if ($numericValue === null) {
            return null;
        }

        if ($numericValue <= 0) {
            $this->addError("Fila {$rowIndex}: Campo '{$fieldName}' debe ser positivo, recibió: {$value}");

            return null;
        }

        return $numericValue;
    }

    /**
     * Add warning
     */
    protected function addWarning(string $message): void
    {
        $this->warnings[] = $message;
        Log::warning('Document Parser Warning', ['message' => $message]);
    }

    /**
     * Add error
     */
    protected function addError(string $message): void
    {
        $this->errors[] = $message;
        Log::error('Document Parser Error', ['message' => $message]);
    }

    /**
     * Add confidence score
     */
    protected function addConfidenceScore(float $score): void
    {
        if ($score >= 0 && $score <= 1) {
            $this->confidenceScores[] = $score;
        }
    }

    /**
     * Calculate average confidence score
     */
    protected function calculateConfidenceScore(): float
    {
        if (empty($this->confidenceScores)) {
            return 0.0;
        }

        return round(array_sum($this->confidenceScores) / count($this->confidenceScores), 2);
    }

    /**
     * Get parsing result
     */
    protected function getResult(array $googleAIResponse): array
    {
        return [
            'items' => $this->items,
            'confidence' => $this->calculateConfidenceScore(),
            'warnings' => $this->warnings,
            'errors' => $this->errors,
            'has_warnings' => ! empty($this->warnings),
            'has_errors' => ! empty($this->errors),
        ];
    }

    /**
     * Reset parser state
     */
    protected function reset(): void
    {
        $this->items = [];
        $this->warnings = [];
        $this->errors = [];
        $this->confidenceScores = [];
    }

    /**
     * Get result with additional metadata
     */
    protected function getResultWithMetadata(array $googleAIResponse, array $metadata = []): array
    {
        $result = $this->getResult($googleAIResponse);

        return array_merge($result, $metadata);
    }
}
