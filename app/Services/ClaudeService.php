<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\Encounter;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
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

            // Log usage for tracking
            if (isset($data['usage'])) {
                $this->logAiUsage(
                    service: 'claude',
                    operation: $options['operation'] ?? 'general',
                    model: $data['model'] ?? $payload['model'],
                    usage: [
                        'input_tokens' => $data['usage']['input_tokens'] ?? 0,
                        'output_tokens' => $data['usage']['output_tokens'] ?? 0,
                        'total_tokens' => ($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0),
                    ],
                    context: array_merge(
                        $options['log_context'] ?? [],
                        ['api_request_id' => $data['id'] ?? null]
                    )
                );
            }

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
                'operation' => 'diagnostic-suggestions',
                'log_context' => [
                    'request_summary' => 'ICD-10 diagnostic suggestions for: '.substr($reason, 0, 100),
                ],
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

    /**
     * Transcribe audio to text using OpenAI Whisper API
     *
     * @param  string  $audioBase64  Base64 encoded audio file
     * @param  string  $mimeType  Audio mime type (audio/webm, audio/mp4, audio/wav, audio/ogg)
     * @return string Transcribed text
     *
     * @throws \Exception
     */
    private function transcribeAudio(string $audioBase64, string $mimeType): string
    {
        $openaiApiKey = config('services.openai.api_key');

        if (empty($openaiApiKey)) {
            throw new \RuntimeException(
                'OpenAI API key not configured. Set OPENAI_API_KEY in .env file to enable voice dictation.'
            );
        }

        try {
            // Decode base64 audio
            $audioData = base64_decode($audioBase64);

            // Determine file extension from mime type
            $extension = match ($mimeType) {
                'audio/webm' => 'webm',
                'audio/mp4', 'audio/m4a' => 'm4a',
                'audio/wav' => 'wav',
                'audio/ogg' => 'ogg',
                default => 'webm',
            };

            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'audio_').'.'.$extension;
            file_put_contents($tempFile, $audioData);

            Log::info('OpenAI Whisper: Transcribing audio', [
                'file_size_mb' => round(strlen($audioData) / 1048576, 2),
                'mime_type' => $mimeType,
                'temp_file' => $tempFile,
            ]);

            // Send to OpenAI Whisper API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$openaiApiKey,
            ])
                ->timeout(config('services.openai.timeout', 60))
                ->attach('file', file_get_contents($tempFile), basename($tempFile))
                ->post(config('services.openai.api_url').'/audio/transcriptions', [
                    'model' => 'whisper-1',
                    'language' => 'es', // Spanish language for medical dictation
                    'response_format' => 'json',
                ]);

            // Clean up temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            if ($response->failed()) {
                Log::error('OpenAI Whisper: Transcription failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('OpenAI Whisper transcription failed: '.$response->body());
            }

            $data = $response->json();
            $transcription = $data['text'] ?? '';

            if (empty($transcription)) {
                throw new \Exception('No se pudo transcribir el audio. El archivo puede estar vacío o dañado.');
            }

            $durationSeconds = $data['duration'] ?? (strlen($audioData) / 16000); // Estimate if not provided

            Log::info('OpenAI Whisper: Transcription successful', [
                'transcription_length' => strlen($transcription),
                'duration_seconds' => $durationSeconds,
            ]);

            // Log usage for tracking
            $this->logAiUsage(
                service: 'openai-whisper',
                operation: 'transcription',
                model: 'whisper-1',
                usage: [
                    'duration_seconds' => $durationSeconds,
                    'total_tokens' => 0, // Whisper doesn't use tokens
                ],
                context: [
                    'audio_size_bytes' => strlen($audioData),
                    'audio_duration_seconds' => (int) round($durationSeconds),
                    'request_summary' => 'Audio transcription for medical dictation',
                ]
            );

            return $transcription;
        } catch (\Exception $e) {
            // Ensure temp file is cleaned up even on error
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }

            Log::error('OpenAI Whisper: Error transcribing audio', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Process medical dictation audio and extract structured clinical information
     *
     * @param  string  $audioBase64  Base64 encoded audio file
     * @param  string  $mimeType  Audio mime type (audio/webm, audio/mp4, audio/wav, audio/ogg)
     * @param  int|null  $encounterId  Optional encounter ID for context
     * @return array Structured medical data extracted from dictation
     *
     * @throws \Exception
     */
    public function processMedicalDictation(string $audioBase64, string $mimeType, ?int $encounterId = null): array
    {
        try {
            // Validate audio format
            $allowedFormats = ['audio/webm', 'audio/mp4', 'audio/m4a', 'audio/wav', 'audio/ogg'];
            if (! in_array($mimeType, $allowedFormats)) {
                throw new \InvalidArgumentException(
                    "Formato de audio no soportado: {$mimeType}. Formatos permitidos: ".implode(', ', $allowedFormats)
                );
            }

            // Validate audio size (max 10MB)
            $maxSize = config('services.claude.voice_dictation_max_file_size', 10485760); // 10MB default
            $audioSize = strlen(base64_decode($audioBase64));
            if ($audioSize > $maxSize) {
                throw new \InvalidArgumentException(
                    sprintf('El archivo de audio excede el tamaño máximo permitido de %.2f MB', $maxSize / 1048576)
                );
            }

            Log::info('Claude AI: Processing medical dictation', [
                'mime_type' => $mimeType,
                'audio_size_mb' => round($audioSize / 1048576, 2),
                'encounter_id' => $encounterId,
            ]);

            // Step 1: Transcribe audio using OpenAI Whisper
            $transcription = $this->transcribeAudio($audioBase64, $mimeType);

            // Step 2: Extract structured medical information from transcription using Claude
            $prompt = $this->buildMedicalDictationPrompt();
            $fullPrompt = "TRANSCRIPCIÓN DEL DICTADO MÉDICO:\n\n{$transcription}\n\n{$prompt}";

            $response = $this->ask($fullPrompt, [
                'temperature' => 0.3, // Low temperature for precise extraction
                'max_tokens' => 4096,
                'system' => 'Eres un asistente médico especializado en extracción de información estructurada de dictados clínicos. Respondes únicamente con JSON válido.',
                'operation' => 'medical-dictation',
                'log_context' => [
                    'encounter_id' => $encounterId,
                    'request_summary' => 'Medical dictation extraction from transcription',
                ],
            ]);

            // Clean response (remove potential markdown code blocks)
            $cleanedResponse = preg_replace('/```json\s*|\s*```/', '', trim($response));

            // Parse JSON response
            $extractedData = json_decode($cleanedResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Claude AI: Failed to parse medical dictation JSON', [
                    'error' => json_last_error_msg(),
                    'response' => $response,
                ]);

                throw new \Exception('Error al procesar la respuesta de IA: '.json_last_error_msg());
            }

            // Add the original transcription to the extracted data
            $extractedData['transcription'] = $transcription;

            // Normalize and validate extracted data
            $normalizedData = $this->normalizeMedicalDictationData($extractedData);

            Log::info('Claude AI: Medical dictation processed successfully', [
                'has_transcription' => isset($normalizedData['transcription']),
                'has_reason' => isset($normalizedData['reason']),
                'vital_signs_count' => count($normalizedData['vital_signs'] ?? []),
                'diagnostics_count' => count($normalizedData['diagnostics'] ?? []),
                'medications_count' => count($normalizedData['medications'] ?? []),
                'service_requests_count' => count($normalizedData['service_requests'] ?? []),
            ]);

            return $normalizedData;
        } catch (\Exception $e) {
            Log::error('Claude AI: Error processing medical dictation', [
                'error' => $e->getMessage(),
                'encounter_id' => $encounterId,
            ]);

            throw $e;
        }
    }

    /**
     * Build the system prompt for medical dictation extraction from transcription
     */
    private function buildMedicalDictationPrompt(): string
    {
        return <<<'EOT'
Tu tarea es extraer información clínica estructurada de la transcripción del dictado médico proporcionado.

Identifica y extrae:
1. Motivo de consulta
2. Enfermedad actual (síntomas, duración, severidad)
3. Signos vitales mencionados
4. Hallazgos del examen físico
5. Diagnósticos
6. Medicamentos prescritos
7. Exámenes de laboratorio solicitados
8. Imágenes diagnósticas solicitadas
9. Procedimientos solicitados

IMPORTANTE:
- Responde ÚNICAMENTE con JSON válido, sin markdown ni texto adicional
- Usa terminología médica precisa en español
- Si no detectas información para un campo, omítelo del JSON
- Para signos vitales, usa códigos LOINC estándar
- Para diagnósticos, incluye código ICD-10 si es mencionado
- Para service requests (laboratorios, imágenes, procedimientos), extrae el nombre descriptivo exacto mencionado

CRÍTICO - SIGNOS VITALES:
- SOLO extrae signos vitales con valores NUMÉRICOS explícitos
- Si se menciona "normal", "dentro de límites normales", "sin alteraciones", etc. - NO lo incluyas
- Si no hay un valor numérico específico, omite ese signo vital del JSON
- Ejemplos VÁLIDOS: "120/80", "36.5", "72", "98"
- Ejemplos INVÁLIDOS: "normal", "estable", "bueno", "sin alteraciones" - estos NO deben incluirse

CRÍTICO - ENFERMEDAD ACTUAL (present_illness):
- **description**: Descripción completa de la enfermedad actual tal como se menciona en el dictado
- **locations**: Array de ubicaciones anatómicas mencionadas (ej: ["hemitórax derecho", "cabeza", "cuello"])
  - "Dolor en hemitórax derecho" → locations: ["hemitórax derecho"]
  - "Dolor de cabeza" → locations: ["cabeza"]
  - "Dolor abdominal" → locations: ["abdomen"]
  - "Dolor de pecho y brazo" → locations: ["pecho", "brazo"]
  - IMPORTANTE: Extrae la ubicación EXACTA mencionada (ej: "hemitórax derecho", no solo "tórax")
- **severity**: Usa EXACTAMENTE uno de estos valores en español: "leve", "moderado", "severo", "incapacitante"
  - "intensidad 6 sobre 10" o "moderado" → "moderado"
  - "intensidad 8 sobre 10" o "intenso" → "severo"
- **timing**: Usa uno de estos valores: "constante", "intermitente", "en la mañana", "en la tarde", "en la noche", "todo el día"
  - "duración continua" o "constante" → "constante"
  - "de vez en cuando" → "intermitente"
- **duration**: Extrae la duración EXACTA mencionada (ej: "hace 3 días", "desde hace cuatro días", "hace una semana")
  - "desde hace tres días" → "desde hace tres días"
  - "hace cuatro días" → "hace cuatro días"
  - NO inventes la duración si no está mencionada
- **aggravating_factors**: Factores que empeoran los síntomas (ej: "empeora con la inspiración profunda")
- **alleviating_factors**: Factores que alivian (ej: "mejora con acetaminofén")
- **associated_symptoms**: Síntomas asociados (ej: "expectoración amarillenta, sensación febril")
- Si NO se menciona explícitamente algún campo, omítelo del JSON

SCHEMA JSON DE RESPUESTA:
{
  "confidence": "high|medium|low",
  "reason": "Motivo principal de consulta",
  "present_illness": {
    "description": "Descripción detallada de la enfermedad actual",
    "severity": "leve|moderado|severo|incapacitante",
    "duration": "hace unos días|hace una semana|hace un mes|hace algunos meses|hace un año",
    "timing": "constante|intermitente|en la mañana|en la tarde|en la noche|todo el día",
    "locations": ["cabeza", "cuello", "pecho", "abdomen"],
    "aggravating_factors": "Factores que empeoran",
    "alleviating_factors": "Factores que alivian",
    "associated_symptoms": "Síntomas asociados"
  },
  "vital_signs": {
    "8480-6": 120,       // Presión arterial sistólica (mmHg) - SOLO valores numéricos
    "8462-4": 80,        // Presión arterial diastólica (mmHg) - SOLO valores numéricos
    "8867-4": 72,        // Frecuencia cardíaca (lpm) - SOLO valores numéricos
    "8310-5": 36.5,      // Temperatura corporal (°C) - SOLO valores numéricos
    "9279-1": 18,        // Frecuencia respiratoria (rpm) - SOLO valores numéricos
    "59408-5": 98,       // Saturación de oxígeno (%) - SOLO valores numéricos
    "8302-2": 170,       // Altura (cm) - SOLO valores numéricos
    "29463-7": 70,       // Peso (kg) - SOLO valores numéricos
    "39156-5": 24.2      // IMC (kg/m²) - SOLO valores numéricos
  },
  "physical_exam": {
    "codigo_loinc": "hallazgo textual"
  },
  "diagnostics": [
    {
      "code": "código ICD-10",
      "description": "Descripción del diagnóstico",
      "confidence": "high|medium|low"
    }
  ],
  "medications": [
    {
      "medication_name": "Nombre del medicamento SIN la dosis",
      "dosage": "Cantidad de unidades por toma (ej: '1 tableta', '2 cápsulas', '10 ml')",
      "frequency": "Frecuencia en horas (ej: 8, 12, 24)",
      "route": "Vía de administración",
      "duration": "Duración del tratamiento en días (ej: '5', '7', '10')",
      "instructions": "Instrucciones completas incluyendo dosis en miligramos"
    }
  ],
  "service_requests": [
    {
      "service_type": "laboratory|images|procedure",
      "description": "Descripción del examen/imagen/procedimiento solicitado",
      "priority": "routine|urgent|asap|stat",
      "note": "Indicaciones o notas adicionales (opcional)"
    }
  ]
}

CÓDIGOS LOINC COMUNES PARA EXAMEN FÍSICO:
- 8716-3: Signos vitales generales
- 10210-3: Examen físico general
- 11384-5: Examen de cabeza
- 11385-2: Examen de ojos
- 11386-0: Examen de oídos
- 11387-8: Examen de nariz
- 11388-6: Examen de garganta
- 11389-4: Examen de cuello
- 11390-2: Examen de tórax
- 11391-0: Examen de pulmones
- 11392-8: Examen cardiovascular
- 11393-6: Examen de abdomen
- 11394-4: Examen de extremidades
- 11395-1: Examen neurológico
- 11396-9: Examen de piel

CRÍTICO - SERVICE REQUESTS (EXÁMENES, IMÁGENES, PROCEDIMIENTOS):
- Extrae TODOS los exámenes de laboratorio mencionados (ej: "hemograma completo", "glucosa", "perfil lipídico", "pruebas de función hepática")
- Extrae TODAS las imágenes diagnósticas solicitadas (ej: "radiografía de tórax", "ecografía abdominal", "tomografía", "resonancia magnética")
- Extrae TODOS los procedimientos solicitados (ej: "electrocardiograma", "espirometría", "biopsia")
- Clasifica cada uno en el service_type correcto:
  - "laboratory": análisis de sangre, orina, heces, cultivos, química sanguínea, hematología, etc.
  - "images": rayos X, ecografías, TAC, resonancias magnéticas, mamografías, etc.
  - "procedure": ECG, espirometrías, biopsias, endoscopias, colonoscopias, pruebas de esfuerzo, etc.
- Para priority, usa:
  - "routine": exámenes de rutina o control
  - "urgent": requiere atención pronta
  - "asap": lo antes posible
  - "stat": inmediato/emergencia

EJEMPLOS DE SERVICE REQUESTS:
- "solicitar hemograma completo" → {"service_type": "laboratory", "description": "Hemograma completo", "priority": "routine"}
- "proteína C reactiva" o "PCR" → {"service_type": "laboratory", "description": "Proteína C reactiva", "priority": "routine"}
- "radiografía de tórax PA y lateral" → {"service_type": "images", "description": "Radiografía de tórax", "priority": "routine", "note": "PA y lateral"}
- "realizar electrocardiograma" → {"service_type": "procedure", "description": "Electrocardiograma", "priority": "routine"}
- "nebulización con salbutamol" → {"service_type": "procedure", "description": "Nebulización", "priority": "routine", "note": "Con salbutamol"}

IMPORTANTE para descripciones:
- Usa nombres GENÉRICOS y SIMPLES (ej: "Hemograma completo", NO "hemograma completo con diferencial")
- Para radiografías, usa solo "Radiografía de [parte del cuerpo]" (ej: "Radiografía de tórax")
- Detalles específicos (PA, lateral, con contraste, etc.) van en el campo "note"

EJEMPLOS DE MEDICAMENTOS (CRÍTICO):
1. "azitromicina quinientos miligramos día uno y luego doscientos cincuenta miligramos diarios por cuatro días"
   → {
       "medication_name": "Azitromicina",
       "dosage": "1 tableta",
       "frequency": "24",
       "route": "oral",
       "duration": "5",
       "instructions": "500 mg el primer día, luego 250 mg diarios por 4 días"
     }

2. "acetaminofén quinientos miligramos cada seis horas si fiebre"
   → {
       "medication_name": "Acetaminofén",
       "dosage": "1 tableta",
       "frequency": "6",
       "route": "oral",
       "duration": "",
       "instructions": "500 mg cada 6 horas si presenta fiebre"
     }

3. "nebulización con salbutamol"
   → {
       "medication_name": "Salbutamol",
       "dosage": "1 dosis",
       "frequency": "",
       "route": "inhalado",
       "duration": "",
       "instructions": "Nebulización"
     }

REGLAS IMPORTANTES PARA MEDICAMENTOS:
- **medication_name**: Solo el nombre del medicamento, SIN dosis en miligramos
- **dosage**: Cantidad de UNIDADES físicas (tabletas, cápsulas, ml), NO miligramos
  - Si se mencion "500 mg" → dosage debe ser "1 tableta" (asumiendo presentación estándar)
  - Si no se especifica la forma, usa "1 dosis"
- **frequency**: Solo el NÚMERO de horas entre cada toma (6, 8, 12, 24)
- **duration**: Solo el NÚMERO de días, NO incluir "días"
- **instructions**: Aquí SÍ incluye todas las dosis en miligramos y detalles completos

RESPONDE SOLO CON EL JSON (sin ```json ni markdown):
EOT;
    }

    /**
     * Normalize and validate extracted medical dictation data
     */
    private function normalizeMedicalDictationData(array $data): array
    {
        $normalized = [];

        // Transcription (required)
        if (isset($data['transcription'])) {
            $normalized['transcription'] = trim($data['transcription']);
        }

        // Confidence level
        if (isset($data['confidence'])) {
            $normalized['confidence'] = $data['confidence'];
        }

        // Reason for encounter
        if (isset($data['reason']) && ! empty($data['reason'])) {
            $normalized['reason'] = trim($data['reason']);
        }

        // Present illness
        if (isset($data['present_illness']) && is_array($data['present_illness'])) {
            $normalized['present_illness'] = array_filter($data['present_illness'], function ($value) {
                return ! empty($value);
            });
        }

        // Vital signs (validate LOINC codes)
        if (isset($data['vital_signs']) && is_array($data['vital_signs'])) {
            $validLoincCodes = ['8480-6', '8462-4', '8867-4', '8310-5', '9279-1', '59408-5', '8302-2', '29463-7', '39156-5'];
            $normalized['vital_signs'] = [];
            foreach ($data['vital_signs'] as $code => $value) {
                if (in_array($code, $validLoincCodes) && ! empty($value)) {
                    $normalized['vital_signs'][$code] = $value;
                }
            }
        }

        // Physical exam findings
        if (isset($data['physical_exam']) && is_array($data['physical_exam'])) {
            $normalized['physical_exam'] = array_filter($data['physical_exam'], function ($value) {
                return ! empty($value);
            });
        }

        // Diagnostics
        if (isset($data['diagnostics']) && is_array($data['diagnostics'])) {
            $normalized['diagnostics'] = array_filter($data['diagnostics'], function ($item) {
                return isset($item['code']) && isset($item['description']);
            });
        }

        // Medications
        if (isset($data['medications']) && is_array($data['medications'])) {
            $normalized['medications'] = array_filter($data['medications'], function ($item) {
                return isset($item['medication_name']);
            });
        }

        // Service Requests (laboratory, images, procedures)
        if (isset($data['service_requests']) && is_array($data['service_requests'])) {
            $normalized['service_requests'] = array_values(array_filter($data['service_requests'], function ($item) {
                // Validate required fields and service_type
                $validServiceTypes = ['laboratory', 'images', 'procedure'];

                return isset($item['service_type'])
                    && in_array($item['service_type'], $validServiceTypes)
                    && isset($item['description'])
                    && ! empty($item['description']);
            }));
        }

        return $normalized;
    }

    /**
     * Log AI usage for tracking and billing
     *
     * @param  string  $service  AI service name (claude, openai-whisper)
     * @param  string  $operation  Operation type (transcription, medical-dictation, diagnostic-suggestions)
     * @param  string  $model  Model used
     * @param  array  $usage  Usage data from API response
     * @param  array  $context  Additional context (encounter_id, patient_id, etc.)
     */
    private function logAiUsage(
        string $service,
        string $operation,
        string $model,
        array $usage,
        array $context = []
    ): void {
        try {
            // Skip logging if no authenticated user
            if (! Auth::check()) {
                return;
            }

            $user = Auth::user();

            // Calculate cost based on service and model
            $costCents = $this->calculateCost($service, $model, $usage);

            // Get client_id from multiple possible sources
            $clientId = $user->client_id
                ?? session('client_id')
                ?? $context['client_id']
                ?? null;

            // If we have an encounter_id, try to get client_id from it
            if (! $clientId && isset($context['encounter_id'])) {
                try {
                    $encounter = Encounter::find($context['encounter_id']);
                    if ($encounter && $encounter->client_id) {
                        $clientId = $encounter->client_id;
                    }
                } catch (\Exception $e) {
                    // Ignore if encounter lookup fails
                }
            }

            $logData = [
                'user_id' => $user->id,
                'client_id' => $clientId,
                'service' => $service,
                'operation' => $operation,
                'model' => $model,
                'input_tokens' => $usage['input_tokens'] ?? 0,
                'output_tokens' => $usage['output_tokens'] ?? 0,
                'total_tokens' => $usage['total_tokens'] ?? 0,
                'estimated_cost_cents' => $costCents,
                'request_summary' => $context['request_summary'] ?? null,
                'audio_duration_seconds' => $context['audio_duration_seconds'] ?? null,
                'audio_size_bytes' => $context['audio_size_bytes'] ?? null,
                'encounter_id' => $context['encounter_id'] ?? null,
                'patient_id' => $context['patient_id'] ?? null,
                'metadata' => $context['metadata'] ?? null,
                'status' => $context['status'] ?? 'success',
                'error_message' => $context['error_message'] ?? null,
                'response_time_ms' => $context['response_time_ms'] ?? null,
                'api_request_id' => $context['api_request_id'] ?? null,
            ];

            AiUsageLog::logUsage($logData);

            Log::info('AI usage logged', [
                'service' => $service,
                'operation' => $operation,
                'tokens' => $usage['total_tokens'] ?? 0,
                'cost_cents' => $costCents,
            ]);
        } catch (\Exception $e) {
            // Don't fail the main operation if logging fails
            Log::error('Failed to log AI usage', [
                'error' => $e->getMessage(),
                'service' => $service,
                'operation' => $operation,
            ]);
        }
    }

    /**
     * Calculate estimated cost in cents based on service, model, and usage
     *
     * @return int Cost in cents
     */
    private function calculateCost(string $service, string $model, array $usage): int
    {
        // Pricing as of 2026 (adjust as needed)
        $pricing = [
            'claude' => [
                'claude-sonnet-4-6' => [
                    'input' => 0.003, // $3 per million input tokens
                    'output' => 0.015, // $15 per million output tokens
                ],
                'claude-opus-4' => [
                    'input' => 0.015, // $15 per million input tokens
                    'output' => 0.075, // $75 per million output tokens
                ],
                'claude-haiku-4' => [
                    'input' => 0.00025, // $0.25 per million input tokens
                    'output' => 0.00125, // $1.25 per million output tokens
                ],
            ],
            'openai-whisper' => [
                'whisper-1' => [
                    'per_second' => 0.0001, // $0.006 per minute = $0.0001 per second
                ],
            ],
        ];

        $cost = 0;

        if ($service === 'claude') {
            $modelPricing = $pricing['claude'][$model] ?? $pricing['claude']['claude-sonnet-4-6'];
            $inputTokens = $usage['input_tokens'] ?? 0;
            $outputTokens = $usage['output_tokens'] ?? 0;

            // Calculate cost per million tokens, then convert to cents
            $cost = ($inputTokens * $modelPricing['input'] / 1000000) +
                    ($outputTokens * $modelPricing['output'] / 1000000);

            $cost *= 100; // Convert dollars to cents
        } elseif ($service === 'openai-whisper') {
            $durationSeconds = $usage['duration_seconds'] ?? 0;
            $modelPricing = $pricing['openai-whisper']['whisper-1'];

            $cost = $durationSeconds * $modelPricing['per_second'] * 100; // Convert to cents
        }

        return (int) round($cost);
    }
}
