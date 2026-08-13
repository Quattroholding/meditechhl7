<?php

namespace App\Jobs;

use App\Ai\Agents\NightwatchExceptionAnalyzer;
use App\Notifications\NightwatchExceptionSolutionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Laravel\Ai\Ai;

class ProcessNightwatchException implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $webhookPayload
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Extraer identificador del issue del webhook
            $issueIdentifier = $this->extractIssueIdentifier();

            if (! $issueIdentifier) {
                Log::warning('No se pudo extraer identificador de issue del webhook Nightwatch', [
                    'payload' => $this->webhookPayload,
                ]);

                return;
            }

            Log::info('Procesando excepción de Nightwatch', [
                'issue_identifier' => $issueIdentifier,
            ]);

            // Obtener detalles completos del issue desde Nightwatch
            $issueData = $this->fetchIssueDetails($issueIdentifier);

            if (! $issueData) {
                Log::error('No se pudieron obtener detalles del issue desde Nightwatch', [
                    'issue_identifier' => $issueIdentifier,
                ]);

                return;
            }

            // Preparar datos para el agente AI
            $exceptionData = $this->prepareExceptionData($issueData);

            // Crear y ejecutar el agente de análisis
            $agent = new NightwatchExceptionAnalyzer($exceptionData);

            Log::info('Ejecutando análisis AI de la excepción');

            // Ejecutar el agente directamente (usa el trait Promptable)
            // El agente ya tiene el prompt en messages(), así que pasamos string vacío
            $response = $agent->prompt(
                prompt: '',
                provider: 'anthropic',
                model: 'claude-sonnet-4-5-20250929'
            );

            $solution = $response->text;

            Log::info('Análisis AI completado', [
                'solution_length' => strlen($solution),
            ]);

            // Enviar notificación por correo
            $this->sendNotification($issueData, $solution);

            Log::info('Solución de excepción enviada por correo', [
                'issue_identifier' => $issueIdentifier,
            ]);

        } catch (\Exception $e) {
            Log::error('Error procesando excepción de Nightwatch', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Extract issue identifier from webhook payload.
     */
    protected function extractIssueIdentifier(): ?string
    {
        // Nightwatch webhooks tienen estructura: { event, timestamp, payload: { issue, ... } }
        // O estructura simple: { event, issue, ... }

        // Intentar primero la estructura anidada
        if (isset($this->webhookPayload['payload']['issue'])) {
            $issue = $this->webhookPayload['payload']['issue'];
        } else {
            $issue = $this->webhookPayload['issue'] ?? null;
        }

        if (!$issue) {
            return null;
        }

        // Retornar URL completa de Nightwatch si está disponible
        if (isset($issue['url'])) {
            return $issue['url'];
        }

        // Fallback a ID o ref
        return $issue['id'] ?? $issue['ref'] ?? null;
    }

    /**
     * Fetch complete issue details from Nightwatch using MCP.
     */
    protected function fetchIssueDetails(string $issueIdentifier): ?array
    {
        try {
            // Usar el MCP de Nightwatch para obtener detalles completos
            $issueData = $this->fetchFromNightwatchMcp($issueIdentifier);

            if ($issueData) {
                return $this->transformNightwatchData($issueData);
            }

            // Fallback: usar datos del webhook si no se pudo obtener del MCP
            Log::warning('Usando datos del webhook como fallback');

            return $this->extractFromWebhookPayload($issueIdentifier);

        } catch (\Exception $e) {
            Log::error('Error obteniendo detalles del issue', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch issue details from Nightwatch MCP.
     */
    protected function fetchFromNightwatchMcp(string $issueIdentifier): ?array
    {
        try {
            // Usar el MCP de Nightwatch (mcp__nightwatch__get_issue)
            // Necesitaríamos llamar al MCP server aquí
            // Por ahora retornamos null para usar el fallback
            return null;
        } catch (\Exception $e) {
            Log::warning('No se pudo obtener issue desde MCP Nightwatch', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Transform Nightwatch MCP data to our format.
     */
    protected function transformNightwatchData(array $data): array
    {
        return [
            'id' => $data['issue']['id'] ?? 'unknown',
            'exception_class' => $data['exception']['class'] ?? 'Unknown',
            'message' => $data['exception']['message'] ?? 'No message',
            'file' => $data['exception']['file'] ?? 'Unknown file',
            'line' => $data['exception']['line'] ?? 0,
            'stack_trace' => $data['stack_trace'] ?? '',
            'code_context' => $data['code_context'] ?? '',
            'execution_context' => $data['execution_context'] ?? [],
            'occurrence_count' => $data['occurrence_count'] ?? 1,
            'first_seen_at' => $data['timestamps']['first_seen_at'] ?? now()->toIso8601String(),
            'last_seen_at' => $data['timestamps']['last_seen_at'] ?? now()->toIso8601String(),
            'nightwatch_url' => $data['issue']['url'] ?? '#',
            'priority' => $data['issue']['priority'] ?? 'medium',
            'status' => $data['issue']['status'] ?? 'open',
        ];
    }

    /**
     * Extract issue details from webhook payload as fallback.
     */
    protected function extractFromWebhookPayload(string $issueIdentifier): array
    {
        // Intentar extraer de estructura anidada primero
        $payload = $this->webhookPayload['payload'] ?? $this->webhookPayload;
        $issue = $payload['issue'] ?? [];
        $details = $issue['details'] ?? [];

        return [
            'id' => $issueIdentifier,
            'exception_class' => $details['class'] ?? $this->webhookPayload['exception']['class'] ?? 'Unknown',
            'message' => $details['message'] ?? $this->webhookPayload['exception']['message'] ?? 'No message',
            'file' => $details['file'] ?? $this->webhookPayload['exception']['file'] ?? 'Unknown file',
            'line' => $details['line'] ?? $this->webhookPayload['exception']['line'] ?? 0,
            'stack_trace' => $this->webhookPayload['exception']['trace'] ?? '',
            'code_context' => $this->webhookPayload['exception']['code_context'] ?? '',
            'execution_context' => $this->webhookPayload['context'] ?? [],
            'occurrence_count' => $this->webhookPayload['occurrence_count'] ?? 1,
            'first_seen_at' => $this->webhookPayload['first_seen_at'] ?? now()->toIso8601String(),
            'last_seen_at' => $this->webhookPayload['last_seen_at'] ?? now()->toIso8601String(),
            'nightwatch_url' => $issue['url'] ?? $this->webhookPayload['issue_url'] ?? '#',
            'priority' => $issue['priority'] ?? $this->webhookPayload['priority'] ?? 'medium',
            'status' => $issue['status'] ?? $this->webhookPayload['status'] ?? 'open',
        ];
    }

    /**
     * Prepare exception data for AI analysis.
     */
    protected function prepareExceptionData(array $issueData): array
    {
        return [
            'exception_class' => $issueData['exception_class'],
            'message' => $issueData['message'],
            'file' => $issueData['file'],
            'line' => $issueData['line'],
            'stack_trace' => $issueData['stack_trace'],
            'code_context' => $issueData['code_context'],
            'execution_context' => $issueData['execution_context'],
            'occurrence_count' => $issueData['occurrence_count'],
            'first_seen_at' => $issueData['first_seen_at'],
            'last_seen_at' => $issueData['last_seen_at'],
        ];
    }

    /**
     * Send notification with the AI-generated solution.
     */
    protected function sendNotification(array $issueData, string $solution): void
    {
        // Obtener emails desde la variable de entorno NIGHTWATCH_ALERT_EMAILS
        $emailsString = config('services.nightwatch.alert_emails', env('NIGHTWATCH_ALERT_EMAILS'));

        if (empty($emailsString)) {
            Log::warning('No hay emails configurados para alertas de Nightwatch');
            return;
        }

        // Convertir string separado por comas a array
        $recipients = array_filter(array_map('trim', explode(',', $emailsString)));

        if (empty($recipients)) {
            Log::warning('Lista de emails de Nightwatch está vacía');
            return;
        }

        Log::info('Enviando notificación de Nightwatch', [
            'recipients' => $recipients,
        ]);

        Notification::route('mail', $recipients)
            ->notify(new NightwatchExceptionSolutionNotification($issueData, $solution));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de procesamiento de excepción Nightwatch falló permanentemente', [
            'payload' => $this->webhookPayload,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
