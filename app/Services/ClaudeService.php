<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    private string $apiKey;

    private string $apiUrl;

    private string $apiVersion;

    private string $defaultModel;

    private int $defaultMaxTokens;

    private int $timeout;

    public function __construct()
    {
        $apiKey = config('services.claude.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'Claude API key not configured. Set CLAUDE_API_KEY in .env file.'
            );
        }

        $this->apiKey = $apiKey;
        $this->apiUrl = rtrim(config('services.claude.api_url'), '/');
        $this->apiVersion = config('services.claude.api_version');
        $this->defaultModel = config('services.claude.default_model');
        $this->defaultMaxTokens = config('services.claude.default_max_tokens');
        $this->timeout = config('services.claude.timeout');
    }

    /**
     * Create HTTP client with authentication headers
     */
    private function httpClient(): PendingRequest
    {
        return Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => $this->apiVersion,
            'content-type' => 'application/json',
        ])
            ->timeout($this->timeout);
    }

    /**
     * Send a message to Claude AI
     *
     * @param  string|array  $messages  Message or array of messages to send
     * @param  array  $options  Additional options (model, max_tokens, temperature, etc.)
     * @return array The complete response from Claude API
     *
     * @throws \Exception
     */
    public function sendMessage(string|array $messages, array $options = []): array
    {
        try {
            // Normalize messages to array format
            $formattedMessages = is_string($messages)
                ? [['role' => 'user', 'content' => $messages]]
                : $messages;

            // Build request payload
            $payload = [
                'model' => $options['model'] ?? $this->defaultModel,
                'max_tokens' => $options['max_tokens'] ?? $this->defaultMaxTokens,
                'messages' => $formattedMessages,
            ];

            // Add optional parameters if provided
            if (isset($options['system'])) {
                $payload['system'] = $options['system'];
            }

            if (isset($options['temperature'])) {
                $payload['temperature'] = $options['temperature'];
            }

            if (isset($options['top_p'])) {
                $payload['top_p'] = $options['top_p'];
            }

            if (isset($options['top_k'])) {
                $payload['top_k'] = $options['top_k'];
            }

            if (isset($options['stop_sequences'])) {
                $payload['stop_sequences'] = $options['stop_sequences'];
            }

            if (isset($options['metadata'])) {
                $payload['metadata'] = $options['metadata'];
            }

            Log::info('Claude AI: Sending message', [
                'model' => $payload['model'],
                'message_count' => count($formattedMessages),
            ]);

            $response = $this->httpClient()->post($this->apiUrl.'/messages', $payload);

            if ($response->failed()) {
                Log::error('Claude AI: Request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Claude API request failed: '.$response->body());
            }

            $data = $response->json();

            Log::info('Claude AI: Message sent successfully', [
                'response_id' => $data['id'] ?? null,
                'model' => $data['model'] ?? null,
                'stop_reason' => $data['stop_reason'] ?? null,
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('Claude AI: Exception occurred', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send a simple text message and get the text response
     *
     * @param  string  $message  The message to send
     * @param  array  $options  Additional options
     * @return string The text response from Claude
     *
     * @throws \Exception
     */
    public function ask(string $message, array $options = []): string
    {
        $response = $this->sendMessage($message, $options);

        return $this->extractTextFromResponse($response);
    }

    /**
     * Send a conversation with multiple messages
     *
     * @param  array  $conversation  Array of messages with role and content
     * @param  array  $options  Additional options
     * @return array The complete response from Claude API
     *
     * @throws \Exception
     */
    public function chat(array $conversation, array $options = []): array
    {
        return $this->sendMessage($conversation, $options);
    }

    /**
     * Extract text content from Claude API response
     *
     * @param  array  $response  The API response
     * @return string The extracted text
     */
    public function extractTextFromResponse(array $response): string
    {
        if (! isset($response['content']) || ! is_array($response['content'])) {
            return '';
        }

        $texts = [];
        foreach ($response['content'] as $contentBlock) {
            if (isset($contentBlock['type']) && $contentBlock['type'] === 'text') {
                $texts[] = $contentBlock['text'];
            }
        }

        return implode("\n", $texts);
    }

    /**
     * Count tokens in the response
     *
     * @param  array  $response  The API response
     * @return array Token usage information
     */
    public function getTokenUsage(array $response): array
    {
        return [
            'input_tokens' => $response['usage']['input_tokens'] ?? 0,
            'output_tokens' => $response['usage']['output_tokens'] ?? 0,
            'total_tokens' => ($response['usage']['input_tokens'] ?? 0) + ($response['usage']['output_tokens'] ?? 0),
        ];
    }

    /**
     * Check if the API is accessible
     */
    public function isAvailable(): bool
    {
        try {
            $response = $this->ask('Hello', ['max_tokens' => 10]);

            return ! empty($response);
        } catch (\Exception $e) {
            Log::warning('Claude AI: Availability check failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Suggest ICD-10 diagnoses based on clinical information
     *
     * @param  string  $reason  Chief complaint / reason for encounter
     * @param  string|null  $presentIllness  Present illness description
     * @param  array  $vitalSigns  Array of vital signs
     * @param  array  $availableCodes  Array of available ICD-10 codes to choose from
     * @return array Array of suggested ICD-10 codes with format ['code' => 'A00', 'description_es' => '...', 'confidence' => 'high']
     *
     * @throws \Exception
     */
    public function suggestDiagnostics(string $reason, ?string $presentIllness = null, array $vitalSigns = [], array $availableCodes = []): array
    {
        try {
            // Build clinical context
            $clinicalContext = "MOTIVO DE CONSULTA:\n{$reason}\n\n";

            if ($presentIllness) {
                $clinicalContext .= "ENFERMEDAD ACTUAL:\n{$presentIllness}\n\n";
            }

            if (! empty($vitalSigns)) {
                $clinicalContext .= "SIGNOS VITALES:\n";
                foreach ($vitalSigns as $vs) {
                    $clinicalContext .= "- {$vs['name']}: {$vs['value']} {$vs['unit']}\n";
                }
                $clinicalContext .= "\n";
            }

            // Build available codes list for reference (limit to first 1000 for token efficiency)
            $codesReference = '';
            if (! empty($availableCodes)) {
                $limitedCodes = array_slice($availableCodes, 0, 1000);
                $codesReference = "\n\nCÓDIGOS ICD-10 DISPONIBLES (selecciona SOLO de esta lista):\n";
                foreach ($limitedCodes as $code) {
                    $codesReference .= "{$code['code']}: {$code['description_es']}\n";
                }
            }

            $prompt = <<<EOT
Eres un asistente médico experto en codificación ICD-10. Tu tarea es sugerir los diagnósticos diferenciales más apropiados basándote en la información clínica proporcionada.

{$clinicalContext}

INSTRUCCIONES IMPORTANTES:
1. Analiza la información clínica cuidadosamente
2. Sugiere entre 3 y 8 diagnósticos diferenciales más probables
3. DEBES responder ÚNICAMENTE con un JSON válido sin texto adicional
4. Los códigos DEBEN existir en la lista de códigos ICD-10 disponibles
5. Ordena los diagnósticos por probabilidad (más probable primero)
6. Incluye tanto diagnósticos específicos como diferenciales importantes

FORMATO DE RESPUESTA (JSON únicamente, sin markdown, sin explicaciones):
[
  {
    "code": "I10",
    "description_es": "Hipertensión esencial (primaria)",
    "confidence": "high",
    "reasoning": "Motivo principal de consulta"
  },
  {
    "code": "E11.9",
    "description_es": "Diabetes mellitus tipo 2 sin complicaciones",
    "confidence": "medium",
    "reasoning": "Común comorbilidad a considerar"
  }
]

NIVELES DE CONFIANZA:
- "high": Muy probable dado el cuadro clínico
- "medium": Posible, requiere evaluación adicional
- "low": Diferencial a considerar/descartar

RESPONDE SOLO CON EL JSON:
EOT;

            Log::info('Claude AI: Requesting diagnostic suggestions', [
                'reason' => $reason,
                'has_present_illness' => ! empty($presentIllness),
                'vital_signs_count' => count($vitalSigns),
            ]);

            $response = $this->ask($prompt, [
                'temperature' => 0.3, // Low temperature for more consistent/precise results
                'max_tokens' => 2048,
                'system' => 'Eres un asistente médico especializado en diagnósticos diferenciales y codificación ICD-10. Respondes únicamente con JSON válido sin texto adicional.',
            ]);

            // Clean response (remove potential markdown code blocks)
            $cleanedResponse = preg_replace('/```json\s*|\s*```/', '', trim($response));

            // Parse JSON response
            $suggestions = json_decode($cleanedResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Claude AI: Failed to parse diagnostic suggestions JSON', [
                    'error' => json_last_error_msg(),
                    'response' => $response,
                ]);

                throw new \Exception('Failed to parse AI response: '.json_last_error_msg());
            }

            // Validate and filter suggestions
            $validSuggestions = [];
            foreach ($suggestions as $suggestion) {
                if (isset($suggestion['code']) && isset($suggestion['description_es'])) {
                    $validSuggestions[] = [
                        'code' => $suggestion['code'],
                        'description_es' => $suggestion['description_es'],
                        'confidence' => $suggestion['confidence'] ?? 'medium',
                        'reasoning' => $suggestion['reasoning'] ?? '',
                    ];
                }
            }

            Log::info('Claude AI: Diagnostic suggestions generated', [
                'suggestions_count' => count($validSuggestions),
            ]);

            return $validSuggestions;
        } catch (\Exception $e) {
            Log::error('Claude AI: Error generating diagnostic suggestions', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
