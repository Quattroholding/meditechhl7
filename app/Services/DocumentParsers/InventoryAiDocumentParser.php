<?php

namespace App\Services\DocumentParsers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InventoryAiDocumentParser extends BaseDocumentParser
{
    private const MAX_TEXT_LENGTH = 50000; // Claude's practical limit

    // Track API usage for cost calculations
    private ?string $modelUsed = null;

    private int $inputTokens = 0;

    private int $outputTokens = 0;

    private float $processingCostUsd = 0.0;

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
                    'model_used' => null,
                    'input_tokens' => null,
                    'output_tokens' => null,
                    'processing_cost_usd' => null,
                ]);
            }

            // Truncate text if too long
            if (strlen($text) > self::MAX_TEXT_LENGTH) {
                $text = substr($text, 0, self::MAX_TEXT_LENGTH);
                $this->addWarning('Documento truncado para procesamiento de IA');
            }

            // Call Claude API
            $claudeResponse = $this->analyzeWithClaude($text);
            $extractedData = $claudeResponse['data'] ?? [];

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
                    'model_used' => $this->modelUsed,
                    'input_tokens' => $this->inputTokens,
                    'output_tokens' => $this->outputTokens,
                    'processing_cost_usd' => $this->processingCostUsd,
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
                'model_used' => $this->modelUsed,
                'input_tokens' => $this->inputTokens,
                'output_tokens' => $this->outputTokens,
                'processing_cost_usd' => $this->processingCostUsd,
            ]);

        } catch (\Exception $e) {
            $this->addError('Error analizando documento: '.$e->getMessage());
            Log::error('InventoryAiDocumentParser error', ['error' => $e->getMessage()]);

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
                'subtotal' => 0,
                'total_tax' => 0,
                'total' => 0,
                'model_used' => $this->modelUsed,
                'input_tokens' => $this->inputTokens,
                'output_tokens' => $this->outputTokens,
                'processing_cost_usd' => $this->processingCostUsd,
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

            $model = config('services.claude.default_model', 'claude-sonnet-4-5');

            $response = Http::withHeaders([
                'x-api-key' => config('services.claude.api_key'),
                'anthropic-version' => config('services.claude.api_version', '2023-06-01'),
            ])->post(config('services.claude.api_url', 'https://api.anthropic.com/v1').'/messages', [
                'model' => $model,
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

            // Extract and store usage information
            $usage = $responseData['usage'] ?? [];
            $this->modelUsed = $model;
            $this->inputTokens = (int) ($usage['input_tokens'] ?? 0);
            $this->outputTokens = (int) ($usage['output_tokens'] ?? 0);

            // Calculate cost based on model
            $this->processingCostUsd = $this->calculateProcessingCost(
                $this->modelUsed,
                $this->inputTokens,
                $this->outputTokens
            );

            Log::info('InventoryAiDocumentParser: Claude API usage', [
                'model' => $this->modelUsed,
                'input_tokens' => $this->inputTokens,
                'output_tokens' => $this->outputTokens,
                'total_tokens' => $this->inputTokens + $this->outputTokens,
                'cost_cents' => $this->processingCostUsd,
            ]);

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

                    // Return data with usage information
                    return [
                        'data' => $data,
                        'usage' => [
                            'model' => $this->modelUsed,
                            'input_tokens' => $this->inputTokens,
                            'output_tokens' => $this->outputTokens,
                            'cost_cents' => $this->processingCostUsd,
                        ],
                    ];
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
Fields may be separated by pipes (|) when extracted from PDF tables.

Extract:
- supplier_name: Company name (look for "Emisor:")
- invoice_number: Invoice number (look for "Número:" or "No. Factura")
- invoice_date: Date in YYYY-MM-DD format (look for "Fecha de emisión" or date like "30/7/2024")
- items: Array of products. For each item line find: sku, name, quantity, unit_cost, base_price, discount_amount, tax_amount, unit_type, internal_units_per_presentation, is_gift
- subtotal: Total before tax (look for "Subtotal" or "Monto Base" or "Subtotal |")
- total_tax: Tax amount (look for "ITBMS" with value after "|" symbol, or "Impuesto")
- total_invoice: Final total (look for "Total" or "Total Neto")
- batch_info: Empty array (batch extraction not needed for now)

Rules for parsing:
1. Product rows may contain: SKU | Description | Quantity | Unit Price | Discount | Amount | Tax | Value
2. SKU is alphanumeric code (e.g., "PFI-BG-640", "470242")
3. Numbers use $ or B/. prefix: "$44.27" → 44.27, remove currency symbols
4. Quantities are decimal numbers: "2.000000" → 2, "10.000000" → 10
5. CONVERSION FACTOR DETECTION (CRITICAL):
   Look for patterns in the product name/description that indicate package size:
   - "X30" or "X 30" or "X30S" → factor = 30
   - "30S" or "30s" at end of name → factor = 30
   - "CAJA X NN" or "CAJA X30" → factor = NN or 30
   - "FRASCO X NN" or "FRASCO X120" → factor = NN or 120
   - "BLISTER X NN" → factor = NN
   - "TAB 30" or "TABS 30" → factor = 30
   - "120ML" in "FRASCO X 120ML" → factor = 120
   Examples:
   - "WELLBUTRIN XL TAB LIB 150MG 30S" → factor = 30 (30 tablets per package)
   - "VITAMINC FRASCO 120ML" → factor = 120
   - "VENTOLIN AEROSOL" (no factor) → factor = 1
   If factor found: internal_units_per_presentation = factor, unit_type = "internal"
   If NO factor: internal_units_per_presentation = 1, unit_type = "presentation"
6. unit_cost: Cost per unit in document. base_price: Selling price. If base_price missing, calculate: base_price = unit_cost × 1.10
7. discount_amount: Extract value from "Descuento" column as-is. Do NOT multiply by quantity. Just copy the number shown.
8. If quantity > 0 but unit_cost = 0, set is_gift=true, include the item anyway
9. Lines may be concatenated with pipes (|) - split and interpret each part logically

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

    /**
     * Calculate processing cost in cents based on model and token usage
     *
     * @param  string  $model  Claude model used
     * @param  int  $inputTokens  Input tokens used
     * @param  int  $outputTokens  Output tokens used
     * @return int Cost in cents (hundredths of USD)
     */
    private function calculateProcessingCost(string $model, int $inputTokens, int $outputTokens): float
    {
        // Pricing as of October 2026 - prices are in dollars
        $pricing = [
            'claude-sonnet-5' => [
                'input' => 3, // $3 per million input tokens
                'output' => 15, // $15 per million output tokens
            ],
            'claude-sonnet-4-5' => [
                'input' => 3, // $3 per million input tokens (same as sonnet-5)
                'output' => 15, // $15 per million output tokens (same as sonnet-5)
            ],
            'claude-opus-4-5-20251101' => [
                'input' => 15, // $15 per million input tokens
                'output' => 75, // $75 per million output tokens
            ],
            'claude-opus-4' => [
                'input' => 15,
                'output' => 75,
            ],
            'claude-haiku-4-5' => [
                'input' => 0.80, // $0.80 per million input tokens
                'output' => 4, // $4 per million output tokens
            ],
            'claude-haiku-4' => [
                'input' => 0.25,
                'output' => 1.25,
            ],
        ];

        $modelPricing = $pricing[$model] ?? $pricing['claude-sonnet-5'];

        // Calculate cost: (tokens / million) * price_per_million = cost_in_dollars
        $costDollars = (($inputTokens * $modelPricing['input']) +
                        ($outputTokens * $modelPricing['output'])) / 1000000;

        return round($costDollars, 4);
    }
}
