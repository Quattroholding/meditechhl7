<?php

/**
 * Ejemplos de uso del servicio ClaudeService
 *
 * Este archivo contiene ejemplos de cómo utilizar el servicio de Claude AI
 * en diferentes escenarios dentro de la aplicación.
 */

namespace App\Services;

// ========================================
// Ejemplo 1: Pregunta simple
// ========================================
function ejemploSimple()
{
    $claude = new ClaudeService;

    // Hacer una pregunta simple y obtener respuesta de texto
    $respuesta = $claude->ask('¿Cuál es la capital de Panamá?');

    echo $respuesta; // "La capital de Panamá es la Ciudad de Panamá..."
}

// ========================================
// Ejemplo 2: Conversación con contexto
// ========================================
function ejemploConversacion()
{
    $claude = new ClaudeService;

    // Crear una conversación con múltiples mensajes
    $conversacion = [
        ['role' => 'user', 'content' => 'Hola, soy un médico'],
        ['role' => 'assistant', 'content' => 'Hola doctor, ¿en qué puedo ayudarte?'],
        ['role' => 'user', 'content' => 'Necesito ayuda para redactar una receta médica'],
    ];

    $response = $claude->chat($conversacion);

    // Extraer el texto de la respuesta
    $texto = $claude->extractTextFromResponse($response);

    echo $texto;
}

// ========================================
// Ejemplo 3: Análisis de síntomas con opciones personalizadas
// ========================================
function ejemploAnalisisSintomas()
{
    $claude = new ClaudeService;

    $sintomas = 'El paciente presenta fiebre de 38.5°C, dolor de cabeza intenso y malestar general desde hace 3 días.';

    $respuesta = $claude->ask(
        "Basándote en estos síntomas, sugiere posibles diagnósticos diferenciales: {$sintomas}",
        [
            'model' => 'claude-sonnet-4-6',
            'max_tokens' => 2048,
            'temperature' => 0.3, // Menos creativo, más preciso
            'system' => 'Eres un asistente médico experto. Proporciona información educativa y diagnósticos diferenciales, pero siempre recuerda que no reemplazas la evaluación médica profesional.',
        ]
    );

    echo $respuesta;
}

// ========================================
// Ejemplo 4: Generar resumen de historia clínica
// ========================================
function ejemploResumenHistoriaClinica()
{
    $claude = new ClaudeService;

    $historiaClinica = '
        Paciente masculino de 45 años con antecedentes de hipertensión arterial
        en tratamiento con enalapril 10mg/día. Consulta por dolor torácico
        opresivo de 2 horas de evolución...
    ';

    $response = $claude->sendMessage(
        "Resume la siguiente historia clínica de forma concisa:\n\n{$historiaClinica}",
        [
            'max_tokens' => 500,
            'temperature' => 0.2,
        ]
    );

    // Obtener el texto de la respuesta
    $resumen = $claude->extractTextFromResponse($response);

    // Obtener información de tokens utilizados
    $tokens = $claude->getTokenUsage($response);
    echo "Resumen: {$resumen}\n";
    echo "Tokens usados: {$tokens['total_tokens']}\n";
}

// ========================================
// Ejemplo 5: Uso en un Controlador
// ========================================
class MedicalAssistantController
{
    public function __construct(
        private ClaudeService $claude
    ) {}

    public function analyzeSymptoms(Request $request)
    {
        try {
            $symptoms = $request->input('symptoms');

            $analysis = $this->claude->ask(
                "Analiza estos síntomas y proporciona posibles diagnósticos diferenciales: {$symptoms}",
                [
                    'system' => 'Eres un asistente médico. Proporciona información educativa pero recuerda que no reemplazas la consulta médica.',
                    'temperature' => 0.3,
                ]
            );

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);
        } catch (\Exception $e) {
            Log::error('Error analyzing symptoms with Claude', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al analizar los síntomas',
            ], 500);
        }
    }
}

// ========================================
// Ejemplo 6: Uso en un Job (Cola)
// ========================================
class GenerateMedicalReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $patientId,
        private string $reportType
    ) {}

    public function handle(ClaudeService $claude): void
    {
        $patient = Patient::findOrFail($this->patientId);

        // Obtener datos del paciente
        $medicalHistory = $patient->medicalHistory;

        // Generar reporte con Claude
        $prompt = "Genera un reporte médico de tipo {$this->reportType} para este paciente...";

        $report = $claude->ask($prompt, [
            'max_tokens' => 4096,
            'temperature' => 0.2,
        ]);

        // Guardar el reporte
        $patient->reports()->create([
            'type' => $this->reportType,
            'content' => $report,
            'generated_at' => now(),
        ]);
    }
}

// ========================================
// Ejemplo 7: Verificar disponibilidad de la API
// ========================================
function verificarDisponibilidad()
{
    $claude = new ClaudeService;

    if ($claude->isAvailable()) {
        echo 'Claude API está disponible';
    } else {
        echo 'Claude API no está disponible en este momento';
    }
}

// ========================================
// Ejemplo 8: Traducción de términos médicos
// ========================================
function ejemploTraduccionMedica()
{
    $claude = new ClaudeService;

    $terminosEnIngles = [
        'Hypertension',
        'Myocardial Infarction',
        'Diabetes Mellitus Type 2',
    ];

    $prompt = 'Traduce los siguientes términos médicos al español y proporciona una breve definición: '.
        implode(', ', $terminosEnIngles);

    $traduccion = $claude->ask($prompt, [
        'temperature' => 0.1, // Muy preciso para traducciones
    ]);

    echo $traduccion;
}

// ========================================
// Ejemplo 9: Análisis de múltiples mensajes con respuesta completa
// ========================================
function ejemploRespuestaCompleta()
{
    $claude = new ClaudeService;

    $response = $claude->sendMessage('Explica qué es la diabetes tipo 2', [
        'max_tokens' => 1024,
    ]);

    // Acceder a todos los datos de la respuesta
    echo "ID del mensaje: {$response['id']}\n";
    echo "Modelo usado: {$response['model']}\n";
    echo "Razón de parada: {$response['stop_reason']}\n";

    // Obtener tokens
    $tokens = $claude->getTokenUsage($response);
    echo "Tokens de entrada: {$tokens['input_tokens']}\n";
    echo "Tokens de salida: {$tokens['output_tokens']}\n";

    // Obtener texto
    $texto = $claude->extractTextFromResponse($response);
    echo "Respuesta: {$texto}\n";
}
