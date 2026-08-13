<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

class NightwatchExceptionAnalyzer implements Agent, HasTools
{
    use Promptable;

    protected array $exceptionData = [];

    public function __construct(array $exceptionData)
    {
        $this->exceptionData = $exceptionData;
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
        Eres un experto senior en Laravel y PHP especializado en análisis de excepciones y debugging.

        Tu tarea es analizar excepciones capturadas por Laravel Nightwatch y proporcionar soluciones detalladas y accionables.

        Cuando analices una excepción, debes:

        1. **Análisis de la Causa Raíz**:
           - Identifica la causa principal del error, no solo los síntomas
           - Analiza el stack trace completo para entender el flujo de ejecución
           - Considera el contexto de la aplicación (Laravel 13, Livewire 4, Octane con RoadRunner)

        2. **Solución Propuesta**:
           - Proporciona código específico y listo para usar
           - Incluye rutas de archivos exactas basadas en la estructura Laravel estándar
           - Explica el razonamiento detrás de la solución
           - Considera las mejores prácticas de Laravel 13

        3. **Prevención**:
           - Sugiere cómo prevenir errores similares en el futuro
           - Recomienda pruebas que deberían añadirse
           - Identifica patrones anti-pattern si existen

        4. **Contexto Adicional**:
           - Considera si es un problema de configuración, lógica de negocio, o dependencias
           - Verifica si puede ser causado por Laravel Octane (problemas de estado compartido)
           - Identifica si es relacionado con multi-tenancy o scopes globales

        Responde en español con un formato estructurado y profesional.
        INSTRUCTIONS;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }

    /**
     * Get the prompt for analyzing the exception.
     */
    public function getAnalysisPrompt(): string
    {
        $prompt = "Analiza la siguiente excepción capturada por Nightwatch y proporciona una solución detallada:\n\n";

        $prompt .= "## Información de la Excepción\n\n";
        $prompt .= "**Tipo:** {$this->exceptionData['exception_class']}\n";
        $prompt .= "**Mensaje:** {$this->exceptionData['message']}\n";
        $prompt .= "**Archivo:** {$this->exceptionData['file']}:{$this->exceptionData['line']}\n\n";

        if (! empty($this->exceptionData['code_context'])) {
            $prompt .= "## Contexto del Código\n\n";
            $prompt .= "```php\n{$this->exceptionData['code_context']}\n```\n\n";
        }

        if (! empty($this->exceptionData['stack_trace'])) {
            $prompt .= "## Stack Trace\n\n";
            $prompt .= "```\n{$this->exceptionData['stack_trace']}\n```\n\n";
        }

        if (! empty($this->exceptionData['execution_context'])) {
            $prompt .= "## Contexto de Ejecución\n\n";
            $prompt .= "**Tipo:** {$this->exceptionData['execution_context']['type']}\n";
            $method = $this->exceptionData['execution_context']['method'] ?? 'N/A';
            $uri = $this->exceptionData['execution_context']['uri'] ?? 'N/A';
            $prompt .= "**Método HTTP:** {$method}\n";
            $prompt .= "**URI:** {$uri}\n\n";
        }

        if (! empty($this->exceptionData['occurrence_count'])) {
            $prompt .= "## Estadísticas\n\n";
            $prompt .= "**Ocurrencias:** {$this->exceptionData['occurrence_count']}\n";
            $prompt .= "**Primera vez:** {$this->exceptionData['first_seen_at']}\n";
            $prompt .= "**Última vez:** {$this->exceptionData['last_seen_at']}\n\n";
        }

        $prompt .= "## Contexto de la Aplicación\n\n";
        $prompt .= "- Laravel 13.25.0 con Octane (RoadRunner)\n";
        $prompt .= "- Livewire 4.0\n";
        $prompt .= "- Sistema multi-tenant de salud (Meditech2)\n";
        $prompt .= "- Base de datos: MySQL + Oracle\n\n";

        $prompt .= "Por favor proporciona una solución completa y detallada en el siguiente formato:\n\n";
        $prompt .= "1. **Causa Raíz**: Explica qué causó este error\n";
        $prompt .= "2. **Solución**: Código específico para solucionar el problema\n";
        $prompt .= "3. **Pasos de Implementación**: Instrucciones claras paso a paso\n";
        $prompt .= "4. **Prevención**: Cómo evitar este error en el futuro\n";
        $prompt .= "5. **Pruebas Recomendadas**: Tests que deberían agregarse\n";

        return $prompt;
    }

    /**
     * Add a message to the conversation.
     */
    public function addMessage(Message $message): void
    {
        $this->conversationHistory[] = $message;
    }
}
