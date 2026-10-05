<?php

namespace App\Services\DocumentParsers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as LaravelLog;

class AnthropicDocumentParser extends BaseDocumentParser
{
    // Track API usage for cost calculations
    private ?string $modelUsed = null;

    private int $inputTokens = 0;

    private int $outputTokens = 0;

    private int $processingCostCents = 0;

    /**
     * Parse document using Claude AI to intelligently extract fields
     *
     * This parser uses the Anthropic API to understand document content
     * and extract relevant fields regardless of format or provider
     */
    public function parse(array $googleAIResponse): array
    {
        $this->reset();

        try {
            $text = $googleAIResponse['document']['text'] ?? '';

            if (empty($text)) {
                $this->addWarning('No se encontró texto extraíble en el documento');

                return $this->getResultWithMetadata($googleAIResponse, [
                    'items' => [],
                    'model_used' => null,
                    'input_tokens' => null,
                    'output_tokens' => null,
                    'processing_cost_cents' => null,
                ]);
            }

            // Use Anthropic API to analyze the document
            $extractedData = $this->analyzeWithClaude($text);

            if (empty($extractedData)) {
                $this->addError('No se pudo extraer información del documento');

                return $this->getResultWithMetadata($googleAIResponse, [
                    'items' => [],
                    'model_used' => $this->modelUsed,
                    'input_tokens' => $this->inputTokens,
                    'output_tokens' => $this->outputTokens,
                    'processing_cost_cents' => $this->processingCostCents,
                ]);
            }

            // Add confidence score based on data completeness
            $confidence = $this->calculateConfidence($extractedData);
            $this->addConfidenceScore($confidence);

            return $this->getResultWithMetadata($googleAIResponse, array_merge(
                ['items' => []],
                $extractedData,
                [
                    'model_used' => $this->modelUsed,
                    'input_tokens' => $this->inputTokens,
                    'output_tokens' => $this->outputTokens,
                    'processing_cost_cents' => $this->processingCostCents,
                ]
            ));

        } catch (\Exception $e) {
            $this->addError('Error analizando documento: '.$e->getMessage());

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
                'model_used' => $this->modelUsed,
                'input_tokens' => $this->inputTokens,
                'output_tokens' => $this->outputTokens,
                'processing_cost_cents' => $this->processingCostCents,
            ]);
        }
    }

    /**
     * Use Claude API to analyze document text
     */
    private function analyzeWithClaude(string $text): array
    {
        try {
            // Truncate text if too large (keep first 10000 chars + last 2000 chars to preserve important info)
            if (strlen($text) > 12000) {
                $firstPart = substr($text, 0, 10000);
                $lastPart = substr($text, -2000);
                $text = $firstPart."\n\n[... documento truncado ...]\n\n".$lastPart;
                LaravelLog::info('AnthropicDocumentParser: Text truncated for Claude API', ['original_length' => strlen($text)]);
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
                LaravelLog::error('Claude API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $responseData = $response->json();
            $responseText = $responseData['content'][0]['text'] ?? '';

            // Extract and store usage information
            $usage = $responseData['usage'] ?? [];
            $this->modelUsed = 'claude-opus-4-5-20251101';
            $this->inputTokens = (int) ($usage['input_tokens'] ?? 0);
            $this->outputTokens = (int) ($usage['output_tokens'] ?? 0);

            // Calculate cost based on model
            $this->processingCostCents = $this->calculateProcessingCost(
                $this->modelUsed,
                $this->inputTokens,
                $this->outputTokens
            );

            LaravelLog::info('AnthropicDocumentParser: Claude API usage', [
                'model' => $this->modelUsed,
                'input_tokens' => $this->inputTokens,
                'output_tokens' => $this->outputTokens,
                'total_tokens' => $this->inputTokens + $this->outputTokens,
                'cost_cents' => $this->processingCostCents,
            ]);

            if (empty($responseText)) {
                LaravelLog::error('Claude API: Empty response', ['response' => $responseData]);

                return [];
            }

            // Extract JSON from response
            if (preg_match('/\{[\s\S]*}/', $responseText, $matches)) {
                $jsonStr = $matches[0];
                $data = json_decode($jsonStr, true);

                if (is_array($data)) {
                    LaravelLog::info('AnthropicDocumentParser: Successfully extracted data', ['fields_count' => count($data)]);

                    return $data;
                } else {
                    LaravelLog::error('AnthropicDocumentParser: Invalid JSON', ['json' => $jsonStr]);
                }
            } else {
                LaravelLog::error('AnthropicDocumentParser: No JSON found in response', ['response' => $responseText]);
            }

            return [];
        } catch (\Exception $e) {
            LaravelLog::error('AnthropicDocumentParser error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    /**
     * Build the prompt for Claude to analyze the document
     */
    private function buildPrompt(string $text): string
    {
        return <<<'PROMPT'
Analiza el siguiente documento y extrae la información en formato JSON válido.

El documento puede ser una factura de servicios (electricidad, agua, gas, internet, teléfono, etc) de cualquier proveedor.

Extrae estos campos si están disponibles:
- bill_number: Número de factura/recibo
- customer_name: Nombre del cliente
- service_address: Dirección de servicio
- billing_period_start: Fecha inicio del período (formato YYYY-MM-DD)
- billing_period_end: Fecha fin del período (formato YYYY-MM-DD)
- issue_date: Fecha de emisión (formato YYYY-MM-DD)
- due_date: Fecha de vencimiento (formato YYYY-MM-DD)
- consumption_value: Cantidad de consumo (número)
- consumption_unit: Unidad de consumo (kWh, m³, GB, etc)
- consumption_kwh: Si es electricidad, consumo en kWh (número)
- consumption_cubic_meters: Si es agua/gas, consumo en m³ (número)
- meter_number: Número del medidor si existe
- subtotal_amount: Subtotal antes de impuestos (número decimal)
- itbms_amount: ITBMS/IVA/ISV (impuesto) (número decimal)
- other_taxes: Otros impuestos si existen (número decimal)
- total_amount: Monto total a pagar (número decimal)
- provider_name: Nombre del proveedor de servicios
- service_type: Tipo de servicio (electricity, water, gas, internet, phone, other)

IMPORTANTE:
1. Retorna SOLO un objeto JSON válido, sin explicaciones adicionales
2. Usa null para campos que no encuentres
3. Convierte números: "1,562" → 1562, "B/. 253.49" → 253.49
4. Convierte fechas al formato YYYY-MM-DD
5. Deduce el tipo de servicio del contenido
6. Extrae ITBMS/impuestos si aparecen en el documento bajo cualquier nombre

DOCUMENTO A ANALIZAR:

PROMPT.$text;
    }

    /**
     * Calculate confidence score based on extracted data
     */
    private function calculateConfidence(array $data): float
    {
        $requiredFields = [
            'bill_number',
            'customer_name',
            'total_amount',
            'service_type',
        ];

        $foundFields = 0;
        foreach ($requiredFields as $field) {
            if (! empty($data[$field])) {
                $foundFields++;
            }
        }

        // Base confidence: percentage of required fields found
        $baseConfidence = $foundFields / count($requiredFields);

        // Bonus confidence if optional fields are present
        $optionalFields = [
            'billing_period_start',
            'billing_period_end',
            'consumption_value',
            'meter_number',
            'subtotal_amount',
            'itbms_amount',
        ];

        $foundOptional = 0;
        foreach ($optionalFields as $field) {
            if (! empty($data[$field])) {
                $foundOptional++;
            }
        }

        $optionalBonus = ($foundOptional / count($optionalFields)) * 0.1;

        return min(0.95, $baseConfidence + $optionalBonus);
    }

    /**
     * Reset parser state including cost tracking
     */
    protected function reset(): void
    {
        parent::reset();
        $this->modelUsed = null;
        $this->inputTokens = 0;
        $this->outputTokens = 0;
        $this->processingCostCents = 0;
    }

    /**
     * Calculate processing cost based on model and token usage
     */
    private function calculateProcessingCost(string $model, int $inputTokens, int $outputTokens): int
    {
        // Pricing as of October 2026 - prices are in dollars
        $pricing = [
            'claude-sonnet-5' => [
                'input' => 3, // $3 per million input tokens
                'output' => 15, // $15 per million output tokens
            ],
            'claude-sonnet-4-5' => [
                'input' => 3, // $3 per million input tokens
                'output' => 15, // $15 per million output tokens
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

        $modelPricing = $pricing[$model] ?? $pricing['claude-opus-4-5-20251101'];

        // Calculate cost: (tokens / million) * price_per_million = cost_in_dollars
        $costDollars = (($inputTokens * $modelPricing['input']) +
                        ($outputTokens * $modelPricing['output'])) / 1000000;

        return (int) round($costDollars * 100);
    }
}
