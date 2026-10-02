<?php

namespace App\Services\DocumentParsers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InventoryAiDocumentParser extends BaseDocumentParser
{
    private const MAX_TEXT_LENGTH = 50000; // Claude's practical limit

    public function parse(array $googleAIResponse): array
    {
        $this->reset();

        try {
            $text = $googleAIResponse['document']['text'] ?? '';

            if (empty($text)) {
                $this->addError('No se encontró texto extraíble en el documento');

                return $this->getResultWithMetadata($googleAIResponse, [
                    'items' => [],
                    'subtotal' => 0,
                    'total_tax' => 0,
                    'total' => 0,
                ]);
            }

            // Truncate text if too long
            if (strlen($text) > self::MAX_TEXT_LENGTH) {
                $text = substr($text, 0, self::MAX_TEXT_LENGTH);
                $this->addWarning('Documento truncado para procesamiento de IA');
            }

            // Call Claude API
            $extractedData = $this->analyzeWithClaude($text);

            if (empty($extractedData) || empty($extractedData['items'])) {
                $itemsCount = count($extractedData['items'] ?? []);
                $this->addError("No se pudieron extraer items del documento (items encontrados: {$itemsCount})");

                Log::warning('InventoryAiDocumentParser: No items extracted', [
                    'extracted_data_keys' => array_keys($extractedData ?? []),
                    'items_count' => $itemsCount,
                    'text_length' => strlen($text),
                ]);

                return $this->getResultWithMetadata($googleAIResponse, [
                    'items' => [],
                    'subtotal' => 0,
                    'total_tax' => 0,
                    'total' => 0,
                ]);
            }

            // Validate and enrich items
            $items = $this->validateAndEnrichItems($extractedData['items']);

            // Calculate totals
            $totals = $this->calculateTotals($items);

            // Validate against invoice total
            if (isset($extractedData['total_invoice']) && $extractedData['total_invoice'] > 0) {
                $diff = abs($totals['total'] - $extractedData['total_invoice']);
                if ($diff > 0.50) {
                    $this->addWarning(sprintf(
                        'Diferencia en total: Calculado $%.2f vs Factura $%.2f',
                        $totals['total'],
                        $extractedData['total_invoice']
                    ));
                }
            }

            // Calculate confidence
            $confidence = $this->calculateConfidence($extractedData, $text);
            $this->addConfidenceScore($confidence);

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => $items,
                'subtotal' => $totals['subtotal'],
                'total_tax' => $totals['total_tax'],
                'total' => $totals['total'],
                'invoice_number' => $extractedData['invoice_number'] ?? null,
                'invoice_date' => $extractedData['invoice_date'] ?? null,
                'supplier_name' => $extractedData['supplier_name'] ?? null,
                'batch_info' => $extractedData['batch_info'] ?? [],
                'detected_format' => 'ai_parsed',
            ]);

        } catch (\Exception $e) {
            $this->addError('Error analizando documento: '.$e->getMessage());
            Log::error('InventoryAiDocumentParser error', ['error' => $e->getMessage()]);

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
                'subtotal' => 0,
                'total_tax' => 0,
                'total' => 0,
            ]);
        }
    }

    /**
     * Call Claude API to analyze invoice text
     */
    private function analyzeWithClaude(string $text): array
    {
        try {
            // Truncate text if too large (keep first 10000 chars + last 2000 chars to preserve important info)
            if (strlen($text) > 12000) {
                $firstPart = substr($text, 0, 10000);
                $lastPart = substr($text, -2000);
                $text = $firstPart."\n\n[... documento truncado ...]\n\n".$lastPart;
                Log::info('InventoryAiDocumentParser: Text truncated for Claude API', ['original_length' => strlen($text)]);
            }

            // Debug: log the actual text being sent - write to separate file for inspection
            file_put_contents(
                storage_path('logs/invoice_text_'.time().'.txt'),
                "=== EXTRACTED TEXT FROM GOOGLE DOCUMENT AI ===\n".
                'Length: '.strlen($text)." characters\n".
                "=== CONTENT ===\n".
                $text
            );

            Log::info('InventoryAiDocumentParser: Extracted text from Google Document AI', [
                'text_length' => strlen($text),
                'has_items_table' => strpos($text, 'Descripción') !== false || strpos($text, 'Producto') !== false,
                'has_invoice_number' => strpos($text, 'Número') !== false || strpos($text, 'Factura') !== false,
                'has_supplier' => strpos($text, 'Emisor') !== false,
            ]);

            $prompt = $this->buildPrompt($text);

            Log::info('InventoryAiDocumentParser: Sending to Claude', [
                'text_length' => strlen($text),
                'prompt_length' => strlen($prompt),
            ]);

            $response = Http::withHeaders([
                'x-api-key' => config('services.claude.api_key'),
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-opus-4-5-20251101',
                'max_tokens' => 4096,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Claude API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $responseData = $response->json();
            $responseText = $responseData['content'][0]['text'] ?? '';

            Log::info('InventoryAiDocumentParser: Claude raw response', [
                'response' => substr($responseText, 0, 500),
            ]);

            if (empty($responseText)) {
                Log::error('Claude API: Empty response', ['response' => $responseData]);

                return [];
            }

            // Extract JSON from response
            if (preg_match('/\{[\s\S]*}/', $responseText, $matches)) {
                $jsonStr = $matches[0];
                $data = json_decode($jsonStr, true);

                if (is_array($data)) {
                    Log::info('InventoryAiDocumentParser: Successfully extracted data', [
                        'items_count' => count($data['items'] ?? []),
                        'extracted_json' => substr(json_encode($data), 0, 500),
                    ]);

                    return $data;
                } else {
                    Log::error('InventoryAiDocumentParser: Invalid JSON', ['json' => $jsonStr]);
                }
            } else {
                Log::error('InventoryAiDocumentParser: No JSON found in response', ['response' => $responseText]);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('InventoryAiDocumentParser error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    /**
     * Build specialized prompt for Claude
     */
    private function buildPrompt(string $text): string
    {
        $prompt = <<<'PROMPT'
Extract pharmacy invoice data. Return ONLY valid JSON object, nothing else.

The text is from a scanned invoice document. Some information may be fragmented across lines.

Extract:
- supplier_name: Company name (look for "Emisor:")
- invoice_number: Invoice number (look for "Número:" or "No. Factura")
- invoice_date: Date in YYYY-MM-DD (look for "Fecha" or date like "28/8/2024")
- items: Array of products. For each item find: sku, name, quantity, unit_cost, base_price, discount_amount, tax_amount, unit_type, internal_units_per_presentation, is_gift
- subtotal: Total before tax (look for "Subtotal" or "Monto Base")
- total_tax: Tax amount (look for "ITBMS" or "Impuesto")
- total_invoice: Final total (look for "Total" or "Total Neto")
- batch_info: Empty array

Rules for parsing:
1. Product code + description might be on separate lines, concatenate them
2. Numbers use $ or B/. prefix: "$44.27" → 44.27
3. Quantities are decimal numbers: "6.000000" → 6
4. If description has "CAJA X 30", set internal_units_per_presentation=30, unit_type="internal" (INTERNAL when factor > 1)
5. If no factor in description, set internal_units_per_presentation=1, unit_type="presentation" (PRESENTATION when factor = 1)
6. unit_cost: Cost per unit (what was paid). base_price: Selling price per unit. If base_price not in document, calculate as: base_price = unit_cost * 1.10
7. discount_amount: The VALUE in the "Descuento Unitario" column - extract EXACTLY what you see there. Do NOT multiply by quantity. Just copy the number.
8. If quantity > 0 but unit_cost = 0, it's a gift: is_gift=true
9. include_gift_items: TRUE - if cost=0, include the item anyway

Output format:
{
  "supplier_name": null,
  "invoice_number": null,
  "invoice_date": null,
  "items": [],
  "subtotal": 0,
  "total_tax": 0,
  "total_invoice": 0,
  "batch_info": []
}

INVOICE TEXT:

PROMPT;

        return $prompt.$text;
    }

    /**
     * Validate and enrich items
     */
    private function validateAndEnrichItems(array $items): array
    {
        $validatedItems = [];

        foreach ($items as $index => $item) {
            // Validate required fields
            if (empty($item['sku']) || empty($item['name'])) {
                $this->addWarning("Item {$index}: SKU o nombre vacíos, se omite");

                continue;
            }

            if (! isset($item['quantity']) || $item['quantity'] === '') {
                $this->addWarning("Item {$index}: Cantidad vacía, se omite");

                continue;
            }

            // Convert and validate numeric fields
            $quantity = $this->validateNumericField($item['quantity'], 'quantity', $index);
            $unitCost = $this->validateNumericField($item['unit_cost'] ?? 0, 'unit_cost', $index);
            $basePrce = $this->validateNumericField($item['base_price'] ?? $item['unit_cost'] ?? 0, 'base_price', $index);
            $discount = $this->validateNumericField($item['discount_amount'] ?? 0, 'discount_amount', $index);
            $tax = $this->validateNumericField($item['tax_amount'] ?? 0, 'tax_amount', $index);

            if ($quantity === null) {
                $this->addWarning("Item {$index}: Cantidad inválida, se omite");

                continue;
            }

            $validatedItem = [
                'sku' => trim((string) $item['sku']),
                'name' => trim((string) $item['name']),
                'quantity' => $quantity,
                'unit_cost' => $unitCost ?? 0,
                'base_price' => $basePrce ?? $unitCost ?? 0,
                'discount_amount' => $discount ?? 0,
                'tax_amount' => $tax ?? 0,
                'unit_type' => $item['unit_type'] ?? 'internal',
                'internal_units_per_presentation' => (int) ($item['internal_units_per_presentation'] ?? 1),
                'is_gift' => (bool) ($item['is_gift'] ?? false),
            ];

            // Ensure unit_type is valid and consistent with internal_units_per_presentation
            if (! in_array($validatedItem['unit_type'], ['internal', 'presentation'])) {
                $validatedItem['unit_type'] = 'internal';
            }

            // Ensure internal_units_per_presentation >= 1
            if ($validatedItem['internal_units_per_presentation'] < 1) {
                $validatedItem['internal_units_per_presentation'] = 1;
            }

            // Auto-correct unit_type based on internal_units_per_presentation
            // If factor > 1, it's an internal unit (e.g., CAJA X 30 is internal)
            // If factor = 1, it's a presentation (single unit)
            if ($validatedItem['internal_units_per_presentation'] > 1) {
                $validatedItem['unit_type'] = 'internal';
            } else {
                $validatedItem['unit_type'] = 'presentation';
            }

            // Auto-correct base_price if invalid
            // base_price should be >= unit_cost and make sense relative to unit_cost
            // If missing or illogical, calculate as unit_cost * 1.10 (10% markup)
            if ($validatedItem['base_price'] <= $validatedItem['unit_cost'] ||
                $validatedItem['base_price'] > $validatedItem['unit_cost'] * 2) {
                $validatedItem['base_price'] = round($validatedItem['unit_cost'] * 1.10, 2);
            }

            // Validate that gifts (cost=0) are marked as is_gift=true
            if ($unitCost === 0.0 && $quantity > 0) {
                $validatedItem['is_gift'] = true;
            }

            $validatedItems[] = $validatedItem;
        }

        return $validatedItems;
    }

    /**
     * Calculate totals
     */
    private function calculateTotals(array $items): array
    {
        $subtotal = 0.0;
        $totalTax = 0.0;

        foreach ($items as $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitCost = (float) ($item['unit_cost'] ?? 0);
            $discount = (float) ($item['discount_amount'] ?? 0);
            $tax = (float) ($item['tax_amount'] ?? 0);

            $lineSubtotal = ($quantity * $unitCost) - ($quantity * $discount);
            $subtotal += $lineSubtotal;
            $totalTax += $tax;
        }

        return [
            'subtotal' => round($subtotal, 2),
            'total_tax' => round($totalTax, 2),
            'total' => round($subtotal + $totalTax, 2),
        ];
    }

    /**
     * Calculate confidence score
     */
    private function calculateConfidence(array $data, string $text): float
    {
        $score = 0.0;

        // Items extracted (weight: 40%)
        $itemsCount = count($data['items'] ?? []);
        if ($itemsCount > 0) {
            $score += min(1.0, $itemsCount / 10) * 0.4;
        }

        // Metadata (weight: 30%)
        if (! empty($data['supplier_name'])) {
            $score += 0.1;
        }
        if (! empty($data['invoice_number'])) {
            $score += 0.1;
        }
        if (! empty($data['invoice_date'])) {
            $score += 0.1;
        }

        // Valid totals (weight: 30%)
        if (isset($data['total_invoice']) && $data['total_invoice'] > 0) {
            $score += 0.3;
        }

        return min(0.95, max(0.6, $score));
    }
}
