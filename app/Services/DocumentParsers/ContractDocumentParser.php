<?php

namespace App\Services\DocumentParsers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as LaravelLog;

class ContractDocumentParser extends BaseDocumentParser
{
    // Track API usage for cost calculations
    private ?string $modelUsed = null;

    private int $inputTokens = 0;

    private int $outputTokens = 0;

    private float $processingCostUsd = 0.0;

    /**
     * Parse contract document using Claude AI to intelligently extract fields
     *
     * This parser uses the Anthropic API to understand contract content
     * and extract relevant fields (parties, dates, terms, conditions, etc.)
     */
    public function parse(array $googleAIResponse): array
    {
        $this->reset();

        try {
            $text = $googleAIResponse['document']['text'] ?? '';
            $model = config('services.claude.default_model', 'claude-sonnet-4-5');

            if (empty($text)) {
                $this->addWarning('No se encontró texto extraíble en el documento');

                return $this->getResultWithMetadata($googleAIResponse, [
                    'items' => [],
                    'model_used' => null,
                    'input_tokens' => null,
                    'output_tokens' => null,
                    'processing_cost_usd' => null,
                ]);
            }

            // Use Anthropic API to analyze the document
            $extractedData = $this->analyzeWithClaude($text, $model);

            if (empty($extractedData)) {
                $this->addError('No se pudo extraer información del contrato');

                return $this->getResultWithMetadata($googleAIResponse, [
                    'items' => [],
                    'model_used' => $this->modelUsed,
                    'input_tokens' => $this->inputTokens,
                    'output_tokens' => $this->outputTokens,
                    'processing_cost_usd' => $this->processingCostUsd,
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
                    'processing_cost_usd' => $this->processingCostUsd,
                ]
            ));

        } catch (\Exception $e) {
            $this->addError('Error analizando documento: '.$e->getMessage());

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
                'model_used' => $this->modelUsed,
                'input_tokens' => $this->inputTokens,
                'output_tokens' => $this->outputTokens,
                'processing_cost_usd' => $this->processingCostUsd,
            ]);
        }
    }

    /**
     * Use Claude API to analyze contract document text
     */
    private function analyzeWithClaude(string $text, string $model): array
    {
        try {
            // Truncate text if too large (keep first 10000 chars + last 2000 chars to preserve important info)
            if (strlen($text) > 12000) {
                $firstPart = substr($text, 0, 10000);
                $lastPart = substr($text, -2000);
                $text = $firstPart."\n\n[... documento truncado ...]\n\n".$lastPart;
                LaravelLog::info('ContractDocumentParser: Text truncated for Claude API', ['original_length' => strlen($text)]);
            }

            $prompt = $this->buildPrompt($text);

            $response = Http::withHeaders([
                'x-api-key' => config('services.claude.api_key'),
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
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
            $this->modelUsed = $model;
            $this->inputTokens = (int) ($usage['input_tokens'] ?? 0);
            $this->outputTokens = (int) ($usage['output_tokens'] ?? 0);

            // Calculate cost based on model
            $this->processingCostUsd = $this->calculateProcessingCost(
                $this->modelUsed,
                $this->inputTokens,
                $this->outputTokens
            );

            LaravelLog::info('ContractDocumentParser: Claude API usage', [
                'model' => $this->modelUsed,
                'input_tokens' => $this->inputTokens,
                'output_tokens' => $this->outputTokens,
                'total_tokens' => $this->inputTokens + $this->outputTokens,
                'cost_cents' => $this->processingCostUsd,
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
                    LaravelLog::info('ContractDocumentParser: Successfully extracted data', ['fields_count' => count($data)]);

                    return $data;
                } else {
                    LaravelLog::error('ContractDocumentParser: Invalid JSON', ['json' => $jsonStr]);
                }
            } else {
                LaravelLog::error('ContractDocumentParser: No JSON found in response', ['response' => $responseText]);
            }

            return [];
        } catch (\Exception $e) {
            LaravelLog::error('ContractDocumentParser error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    /**
     * Build the prompt for Claude to analyze the contract document
     */
    private function buildPrompt(string $text): string
    {
        return <<<'PROMPT'
Analiza el siguiente documento de contrato y extrae la información en formato JSON válido.

Los contratos pueden ser de varios tipos:
1. Contrato de alquiler/arrendamiento de propiedad
2. Contrato de servicios
3. Contrato de compraventa
4. Contrato de préstamo
5. Acuerdo entre partes

CAMPOS A EXTRAER:
- contract_type: Tipo de contrato (rental, services, purchase, loan, agreement, other)
- contract_number: Número de identificación del contrato
- contract_title: Título o nombre del contrato
- start_date: Fecha de inicio (formato YYYY-MM-DD)
- end_date: Fecha de finalización (formato YYYY-MM-DD)
- party_1_name: Nombre de la primera parte (ej: arrendador/propietario/vendedor)
- party_1_type: Tipo de parte 1 (individual, legal_entity)
- party_1_id: Cédula/RUC/DUI de la parte 1
- party_2_name: Nombre de la segunda parte (ej: arrendatario/inquilino/comprador)
- party_2_type: Tipo de parte 2 (individual, legal_entity)
- party_2_id: Cédula/RUC/DUI de la parte 2
- property_address: Dirección de propiedad (si aplica al contrato)
- property_type: Tipo de propiedad (apartment, house, commercial, land, other)
- contract_value: Valor monetario del contrato (número decimal)
- currency: Moneda (PAB, USD, etc)
- payment_frequency: Frecuencia de pago (monthly, quarterly, annual, one_time, other)
- payment_amount: Monto del pago periódico (número decimal)
- renewal_terms: Términos de renovación (automatic, upon_agreement, not_renewable, other)
- termination_conditions: Condiciones de terminación (text breve, máximo 200 caracteres)
- early_termination_penalty: Penalidad por terminación anticipada (text breve)
- key_clauses: Array de cláusulas principales (máximo 5 cláusulas)
- special_conditions: Condiciones especiales o notas importantes
- signatures_present: Booleano indicando si contiene firmas
- signature_dates: Fecha(s) de firma (formato YYYY-MM-DD)

INSTRUCCIONES CRÍTICAS:
1. Retorna SOLO un objeto JSON válido, sin explicaciones adicionales
2. Usa null para campos que no encuentres
3. Convierte números: "1,562" → 1562, "B/. 253.49" → 253.49
4. Convierte fechas al formato YYYY-MM-DD
5. Limpia números: elimina espacios, guiones innecesarios
6. Para campos de texto, proporciona información concisa
7. Para key_clauses, extrae cláusulas principales como strings en un array
8. Para property_address, busca en descripción de bienes o ubicación
9. Identifica correctamente party_1 y party_2:
   - Para contratos de ALQUILER: party_1 = ARRENDADOR/PROPIETARIO (quien recibe pago), party_2 = ARRENDATARIO/INQUILINO (quien paga)
   - Para otros: party_1 = quien propone/vende, party_2 = quien acepta/compra

EJEMPLO DE RESPUESTA:
{
  "contract_type": "rental",
  "contract_number": "2024-001",
  "contract_title": "Contrato de Arrendamiento de Propiedad",
  "start_date": "2024-01-01",
  "end_date": "2025-12-31",
  "party_1_name": "Juan Pérez López",
  "party_1_type": "individual",
  "party_1_id": "8-123-456",
  "party_2_name": "María González García",
  "party_2_type": "individual",
  "party_2_id": "8-654-321",
  "property_address": "Calle Principal 123, Apt 4B, Ciudad de Panamá",
  "property_type": "apartment",
  "contract_value": 50000,
  "currency": "PAB",
  "payment_frequency": "monthly",
  "payment_amount": 500,
  "renewal_terms": "automatic",
  "termination_conditions": "Con 30 días de aviso previo",
  "early_termination_penalty": "Pérdida de depósito de garantía",
  "key_clauses": [
    "Depósito de garantía equivalente a 2 meses de renta",
    "El arrendador es responsable de mantenimiento de estructuras",
    "Prohibido subarrendar sin consentimiento escrito",
    "Pago de servicios incluidos en la renta",
    "Revisión de renta anual del 3%"
  ],
  "special_conditions": "Se incluye estacionamiento subterráneo",
  "signatures_present": true,
  "signature_dates": "2024-01-01"
}

DOCUMENTO A ANALIZAR:

PROMPT.$text;
    }

    /**
     * Calculate confidence score based on extracted data
     */
    private function calculateConfidence(array $data): float
    {
        $requiredFields = [
            'contract_type',
            'party_1_name',
            'party_2_name',
            'start_date',
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
            'contract_number',
            'end_date',
            'property_address',
            'contract_value',
            'payment_frequency',
            'key_clauses',
            'signatures_present',
        ];

        $foundOptional = 0;
        foreach ($optionalFields as $field) {
            if (! empty($data[$field])) {
                $foundOptional++;
            }
        }

        $optionalBonus = ($foundOptional / count($optionalFields)) * 0.15;

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
        $this->processingCostUsd = 0;
    }

    /**
     * Calculate processing cost based on model and token usage
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

        $modelPricing = $pricing[$model] ?? $pricing['claude-sonnet-4-5'];

        // Calculate cost: (tokens / million) * price_per_million = cost_in_dollars
        $costDollars = (($inputTokens * $modelPricing['input']) +
                        ($outputTokens * $modelPricing['output'])) / 1000000;

        return round($costDollars, 4);
    }
}
