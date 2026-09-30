<?php

namespace App\Services\DocumentParsers;

class GenericDocumentParser extends BaseDocumentParser
{
    /**
     * Parse Google Document AI response generically for any document type
     *
     * Extracts all available data without assuming specific structure
     */
    public function parse(array $googleAIResponse): array
    {
        $this->reset();

        try {
            // Extract full text
            $fullText = $googleAIResponse['document']['text'] ?? '';

            // Extract tables (all of them)
            $tables = $this->extractAllTables($googleAIResponse);

            // Extract entities
            $entities = $googleAIResponse['document']['entities'] ?? [];

            // Extract key-value pairs from text
            $keyValuePairs = $this->extractKeyValuePairs($fullText);

            // Extract numeric fields
            $numericFields = $this->extractNumericFields($fullText);

            // Extract lines for display
            $lines = array_filter(
                array_map('trim', explode("\n", $fullText))
            );

            // Add confidence scores
            $this->addConfidenceScore(0.80);

            // Return result
            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [], // Generic documents don't have structured items
                'full_text' => $fullText,
                'tables' => $tables,
                'entities' => $entities,
                'key_value_pairs' => $keyValuePairs,
                'numeric_fields' => $numericFields,
                'lines' => array_values($lines),
            ]);

        } catch (\Exception $e) {
            $this->addError('Error procesando documento: '.$e->getMessage());

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
                'full_text' => '',
                'tables' => [],
                'entities' => [],
                'key_value_pairs' => [],
                'numeric_fields' => [],
                'lines' => [],
            ]);
        }
    }

    /**
     * Extract all tables from the document
     */
    private function extractAllTables(array $googleAIResponse): array
    {
        $tables = [];

        if (! isset($googleAIResponse['document']['pages'])) {
            return $tables;
        }

        foreach ($googleAIResponse['document']['pages'] as $pageIndex => $page) {
            if (! isset($page['tables'])) {
                continue;
            }

            foreach ($page['tables'] as $tableIndex => $table) {
                if (! isset($table['body'])) {
                    continue;
                }

                $tableData = [];

                // Convert table to 2D array
                foreach ($table['body'] as $row) {
                    $rowData = [];
                    foreach ($row['cells'] ?? [] as $cell) {
                        $cellText = $this->extractCellText($cell);
                        $rowData[] = $cellText;
                    }
                    if (! empty(array_filter($rowData))) {
                        $tableData[] = $rowData;
                    }
                }

                if (! empty($tableData)) {
                    $tables[] = [
                        'page' => $pageIndex,
                        'index' => $tableIndex,
                        'rows' => $tableData,
                    ];
                }
            }
        }

        return $tables;
    }

    /**
     * Extract cell text from nested structure
     */
    private function extractCellText(array $cell): string
    {
        // Try normalized_text first
        if (isset($cell['normalizedText'])) {
            return trim($cell['normalizedText']);
        }

        // Try layout.textAnchor next
        if (isset($cell['layout']['textAnchor']['textSegments'])) {
            $text = '';
            foreach ($cell['layout']['textAnchor']['textSegments'] as $segment) {
                $text .= $segment['startIndex'] ?? '';
            }
            if (! empty($text)) {
                return trim($text);
            }
        }

        return '';
    }

    /**
     * Extract key-value pairs from text using regex
     *
     * Looks for patterns like:
     * - "Nombre: Juan Pérez"
     * - "Factura N°: 12345"
     * - "Total: $100.00"
     */
    private function extractKeyValuePairs(string $text): array
    {
        $pairs = [];

        if (empty($text)) {
            return $pairs;
        }

        // Pattern: "Key: Value" or "Key = Value"
        $pattern = '/([\w\s\-\.]+)\s*[:=]\s*([^\n]+)/';

        if (preg_match_all($pattern, $text, $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $key = trim($matches[1][$i]);
                $value = trim($matches[2][$i]);

                // Skip header-like keys
                if ($this->isHeaderKey($key)) {
                    continue;
                }

                // Skip very short keys
                if (strlen($key) < 3) {
                    continue;
                }

                // Skip if key appears to be part of a sentence
                if (strlen($key) > 50) {
                    continue;
                }

                // Avoid duplicates
                if (! isset($pairs[$key])) {
                    $pairs[$key] = $value;
                }
            }
        }

        return $pairs;
    }

    /**
     * Check if a key is a header/label to skip
     */
    private function isHeaderKey(string $key): bool
    {
        $lowerKey = strtolower(trim($key));

        $headerKeywords = [
            'page', 'página', 'factura', 'invoice', 'document',
            'tabla', 'table', 'field', 'header', 'item', 'row',
            'fecha de', 'date of', 'total items', 'número de',
        ];

        foreach ($headerKeywords as $keyword) {
            if (strpos($lowerKey, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract numeric fields with context
     *
     * Looks for patterns like:
     * - "$100.00", "B/.50.25"
     * - "140 kWh", "50 litros"
     */
    private function extractNumericFields(string $text): array
    {
        $fields = [];

        if (empty($text)) {
            return $fields;
        }

        // Pattern: optional currency, number, optional unit
        $pattern = '/([A-Z$€Β\/]?)\s*([\d,\.]+)\s*([a-zA-Z%\s]*)/';

        if (preg_match_all($pattern, $text, $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $currency = trim($matches[1][$i]);
                $value = $this->normalizeNumber($matches[2][$i]);
                $unit = trim($matches[3][$i]);

                if (empty($value) || $value == 0) {
                    continue;
                }

                // Skip very small or very large numbers that are likely OCR errors
                if ($value < 0.01 || $value > 999999999) {
                    continue;
                }

                // Create label
                $label = '';
                if (! empty($currency)) {
                    $label .= $currency.' ';
                }
                if (! empty($unit) && ! empty($currency)) {
                    $label .= $unit;
                } elseif (! empty($unit)) {
                    $label = $unit;
                } else {
                    $label = 'Monto';
                }

                $fields[] = [
                    'label' => trim($label) ?: 'Monto',
                    'value' => $value,
                    'currency' => $currency ?: null,
                    'unit' => $unit ?: null,
                ];
            }
        }

        return $fields;
    }

    /**
     * Normalize numeric string to float
     */
    private function normalizeNumber(string $numberStr): float
    {
        // Remove spaces
        $number = str_replace(' ', '', $numberStr);

        // Replace comma with dot for decimals
        $number = str_replace(',', '.', $number);

        // Remove non-numeric characters except dot
        $number = preg_replace('/[^\d\.]/', '', $number);

        // Handle multiple dots (keep last one as decimal)
        if (substr_count($number, '.') > 1) {
            $parts = explode('.', $number);
            $decimal = array_pop($parts);
            $integer = implode('', $parts);
            $number = $integer.'.'.$decimal;
        }

        return (float) $number;
    }
}
