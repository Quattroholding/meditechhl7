<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessNightwatchException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NightwatchWebhookController extends Controller
{
    /**
     * Handle incoming Nightwatch webhook.
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Verificar firma del webhook (si Nightwatch lo soporta)
            if (! $this->verifySignature($request)) {
                Log::warning('Intento de webhook Nightwatch con firma inválida', [
                    'ip' => $request->ip(),
                ]);

                return response()->json(['error' => 'Invalid signature'], 403);
            }

            $payload = $request->all();

            Log::info('Webhook Nightwatch recibido', [
                'event_type' => $payload['event'] ?? 'unknown',
                'issue_id' => $payload['issue']['id'] ?? null,
                'full_payload' => $payload, // Debug: ver payload completo
            ]);

            // Filtrar solo eventos de nuevas excepciones
            if (! $this->shouldProcessWebhook($payload)) {
                return response()->json(['status' => 'ignored']);
            }

            // Despachar job para procesamiento asíncrono
            ProcessNightwatchException::dispatch($payload);

            Log::info('Job de procesamiento de excepción despachado');

            return response()->json(['status' => 'received']);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook de Nightwatch', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Verify webhook signature.
     */
    protected function verifySignature(Request $request): bool
    {
        // Implementar verificación de firma si Nightwatch lo soporta
        // Por ahora, verificamos que venga del webhook configurado
        $secret = config('services.nightwatch.webhook_secret');

        if (! $secret) {
            // Si no hay secreto configurado, aceptar (desarrollo)
            return true;
        }

        $signature = $request->header('Nightwatch-Signature');

        if (! $signature) {
            Log::warning('Nightwatch webhook sin header Nightwatch-Signature', [
                'headers' => $request->headers->all(),
                'ip' => $request->ip(),
            ]);

            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        Log::info('Verificación de firma Nightwatch', [
            'signature_received' => $signature,
            'signature_expected' => $expectedSignature,
            'payload_length' => strlen($payload),
            'secret_length' => strlen($secret),
        ]);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Determine if the webhook should be processed.
     */
    protected function shouldProcessWebhook(array $payload): bool
    {
        // Procesar solo eventos de nuevas excepciones o excepciones reabiertas
        $eventType = $payload['event'] ?? '';

        if (! in_array($eventType, ['exception.created', 'exception.reopened', 'issue.created', 'issue.opened', 'issue.reopened'])) {
            Log::info('Webhook Nightwatch ignorado por tipo de evento', [
                'event_type' => $eventType,
            ]);

            return false;
        }

        // Filtrar por prioridad si está configurado
        $minPriority = config('services.nightwatch.min_priority_for_ai', 'medium');

        // Extraer prioridad de la estructura anidada del webhook
        $issuePriority = $payload['payload']['issue']['priority']
                         ?? $payload['issue']['priority']
                         ?? $payload['priority']
                         ?? 'none';

        // Mapeo de prioridades (none = 0, low = 1, medium = 2, high = 3)
        $priorities = ['none' => 0, 'low' => 1, 'medium' => 2, 'high' => 3];

        // Si la prioridad mínima no está en el array, usar 'medium' como default
        if (! isset($priorities[$minPriority])) {
            $minPriority = 'medium';
        }

        // Si la prioridad del issue no está en el array, usar 'none' como default
        if (! isset($priorities[$issuePriority])) {
            $issuePriority = 'none';
        }

        $shouldProcess = $priorities[$issuePriority] >= $priorities[$minPriority];

        Log::info('Filtrado por prioridad de Nightwatch', [
            'issue_priority' => $issuePriority,
            'min_priority_required' => $minPriority,
            'will_process' => $shouldProcess,
        ]);

        return $shouldProcess;
    }
}
