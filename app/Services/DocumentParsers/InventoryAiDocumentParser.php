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

            $prompt = $this->buildPrompt($text);

            $response = Http::withHeaders([
                'x-api-key' => config('services.claude.api_key'),
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-opus-4-5-20251101',
                'max_tokens' => 1024,
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

            if (empty($responseText)) {
                Log::error('Claude API: Empty response', ['response' => $responseData]);

                return [];
            }

            // Extract JSON from response
            if (preg_match('/\{[\s\S]*}/', $responseText, $matches)) {
                $jsonStr = $matches[0];
                $data = json_decode($jsonStr, true);

                if (is_array($data)) {
                    Log::info('InventoryAiDocumentParser: Successfully extracted data', ['items_count' => count($data['items'] ?? [])]);

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
        return <<<'PROMPT'
Eres un parser especializado en facturas de inventario farmacéutico de Panamá.

CONTEXTO:
- Proveedores: Reprico, Impaduel, Haseth, y similares
- Productos: Medicamentos con presentaciones (tabletas, cápsulas, jarabes, etc.)
- Formato: Facturas electrónicas DGI de Panamá

REGLAS DE EXTRACCIÓN:

1. METADATA (siempre extraer):
   - supplier_name: Empresa emisora de la factura
   - invoice_number: Número de factura
   - invoice_date: Fecha en formato YYYY-MM-DD

2. ITEMS (extraer TODOS los productos):

   Para cada producto:
   - sku: Código del producto
   - name: Nombre completo incluyendo concentración (ej: "CONCOR 5.00 mg")
   - quantity: Cantidad (número, puede ser decimal)
   - unit_cost: Costo por unidad (número decimal)
   - base_price: Precio de venta por unidad (si diferente de unit_cost, sino usar unit_cost)
   - discount_amount: Descuento POR UNIDAD en valor absoluto (número)
   - tax_amount: Impuesto/ITBMS para esta línea (número)
   - unit_type: "internal" o "presentation"
   - internal_units_per_presentation: Factor de conversión (número, default 1)
   - is_gift: true si es un regalo/bonificación del proveedor

3. DETECCIÓN DE FACTOR DE CONVERSIÓN:

   Si la descripción contiene patrones como:
   - "CAJA X 30 TABLETAS" → internal_units_per_presentation = 30, unit_type = "presentation"
   - "FRASCO X 120 ML" → internal_units_per_presentation = 120, unit_type = "presentation"
   - "BLISTER X 10 CAPS" → internal_units_per_presentation = 10, unit_type = "presentation"
   - "SOBRE X 24 SOBRES" → internal_units_per_presentation = 24, unit_type = "presentation"

   Extrae el número y establece unit_type = "presentation"
   Si no hay patrón, usa internal_units_per_presentation = 1, unit_type = "internal"

4. MANEJO DE DESCUENTOS:

   Si el descuento aparece como porcentaje:
   - Calcula: discount_amount = (unit_cost * porcentaje / 100)

   Si aparece como monto:
   - Usa el monto directamente como discount_amount

5. REGALOS Y BONIFICACIONES (MUY IMPORTANTE):

   En facturas de Impaduel y otros proveedores, verás items repetidos donde:
   - El primero tiene cantidad > 0 y unit_cost > 0 (compra regular)
   - El segundo tiene cantidad > 0 pero unit_cost = 0 (REGALO del proveedor)

   Ejemplo:
   - "001-470242 - WELLBUTRIN XL" Cantidad: 10, Costo: 82.68 → item regular
   - "002-470242 - WELLBUTRIN XL" Cantidad: 1, Costo: 0.00 → REGALO

   DEBES INCLUIR AMBOS ITEMS:
   - El primero con is_gift = false
   - El segundo con is_gift = true

   NO filtres ni ignores items con costo $0.00, son regalos legítimos que entran al inventario.

6. INFORMACIÓN DE LOTE (opcional):
   - Si hay "Lote y Fvenc.WE7G 30-11-2025", extrae batch_code y expiration_date
   - Almacena en batch_info

7. TOTALES:
   - subtotal: Suma de (cantidad × costo_unitario - descuento) para TODOS los items
   - total_tax: Suma de todos los impuestos
   - total_invoice: subtotal + total_tax

FORMATO DE SALIDA:

Retorna SOLO JSON válido (sin markdown, sin explicaciones):

{
  "supplier_name": "string o null",
  "invoice_number": "string o null",
  "invoice_date": "YYYY-MM-DD o null",
  "items": [
    {
      "sku": "string",
      "name": "string",
      "quantity": number,
      "unit_cost": number,
      "base_price": number,
      "discount_amount": number,
      "tax_amount": number,
      "unit_type": "internal" o "presentation",
      "internal_units_per_presentation": number,
      "is_gift": boolean
    }
  ],
  "subtotal": number,
  "total_tax": number,
  "total_invoice": number,
  "batch_info": []
}

DOCUMENTO A ANALIZAR:

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

            // Ensure unit_type is valid
            if (! in_array($validatedItem['unit_type'], ['internal', 'presentation'])) {
                $validatedItem['unit_type'] = 'internal';
            }

            // Ensure internal_units_per_presentation >= 1
            if ($validatedItem['internal_units_per_presentation'] < 1) {
                $validatedItem['internal_units_per_presentation'] = 1;
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
