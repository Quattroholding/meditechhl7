<?php

namespace App\Listeners;

use App\Services\SendItemsSyncService;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SaveEmailToSentItems
{
    /**
     * Crear el listener
     */
    public function __construct()
    {
        //
    }

    /**
     * Manejar el evento de correo enviado
     */
    public function handle(MessageSent $event): void
    {
        // Obtener info del mensaje para identificarlo
        $message = $event->sent->getOriginalMessage();
        $subject = $message->getSubject() ?? '(Sin asunto)';
        $messageId = spl_object_hash($event);

        Log::info('=== SaveEmailToSentItems START ===', [
            'subject' => $subject,
            'event_id' => $messageId,
            'timestamp' => now()->format('Y-m-d H:i:s.u'),
        ]);

        // Solo guardar si está habilitado en config
        if (! config('services.microsoft.save_to_sent_items', true)) {
            Log::info('SaveEmailToSentItems deshabilitado en config, saltando');

            return;
        }

        try {

            // Extraer información del mensaje
            $subject = $message->getSubject() ?? '(Sin asunto)';
            $to = $this->extractRecipients($message->getTo());

            // Generar un ID único para este mensaje basado en su contenido (sin timestamp para evitar duplicados)
            $messageHash = md5($subject.serialize($to));
            $cacheKey = "sent-item-processed:{$messageHash}";

            // Verificar si este mensaje ya fue procesado (Cache::add es atómico)
            // Si la clave ya existe, add() retorna false
            $wasAdded = Cache::add($cacheKey, true, 3600); // 1 hora de TTL

            if (! $wasAdded) {
                Log::info('Correo ya fue procesado previamente, saltando duplicado', [
                    'subject' => $subject,
                    'cache_key' => $cacheKey,
                    'event_id' => $messageId,
                ]);

                return;
            }

            try {
                $syncService = new SendItemsSyncService;

                $htmlBody = $message->getHtmlBody() ?? '';

                // Si no hay HTML, usar texto plano
                if (empty($htmlBody)) {
                    $textBody = $message->getTextBody() ?? '';
                    $htmlBody = '<pre>'.htmlspecialchars($textBody).'</pre>';
                }

                // Extraer destinatarios CC y BCC
                $cc = $this->extractRecipients($message->getCc());
                $bcc = $this->extractRecipients($message->getBcc());

                // Extraer headers personalizados (X-*)
                $customHeaders = [];
                $allHeaderNames = [];
                $headers = $message->getHeaders();

                foreach ($headers->all() as $name => $header) {
                    $allHeaderNames[] = $name;
                    // Buscar headers que empiecen con X- (case insensitive)
                    if (str_starts_with(strtolower($name), 'x-')) {
                        $customHeaders[$name] = $header->getBodyAsString();
                    }
                }

                Log::info('Headers del mensaje analizados', [
                    'total_headers' => count($allHeaderNames),
                    'x_headers_count' => count($customHeaders),
                    'x_headers' => $customHeaders,
                    'subject' => $subject,
                ]);

                // Guardar en Sent Items
                $syncService->saveSentEmail($to, $subject, $htmlBody, $cc, $bcc, $customHeaders);

                Log::info('Correo sincronizado a Sent Items', [
                    'subject' => $subject,
                    'recipients_count' => count($to),
                    'cache_key' => $cacheKey,
                    'event_id' => $messageId,
                ]);

                Log::info('=== SaveEmailToSentItems END ===', [
                    'subject' => $subject,
                    'event_id' => $messageId,
                    'timestamp' => now()->format('Y-m-d H:i:s.u'),
                ]);
            } catch (\Exception $innerException) {
                // Si falla, remover la marca de procesado para permitir reintento
                Cache::forget($cacheKey);
                throw $innerException;
            }
        } catch (\Exception $e) {
            // Loguear el error pero no fallar el envío del correo
            Log::error('Error guardando correo en Sent Items: '.$e->getMessage(), [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $messageId ?? 'unknown',
            ]);

            Log::info('=== SaveEmailToSentItems END (WITH ERROR) ===', [
                'subject' => $subject,
                'event_id' => $messageId,
                'timestamp' => now()->format('Y-m-d H:i:s.u'),
            ]);
        }
    }

    /**
     * Extraer destinatarios en formato array
     */
    protected function extractRecipients($addresses): array
    {
        if (empty($addresses)) {
            return [];
        }

        $recipients = [];

        foreach ($addresses as $address) {
            if (is_object($address)) {
                $email = method_exists($address, 'getAddress') ? $address->getAddress() : (string) $address;
                $name = method_exists($address, 'getName') ? $address->getName() : null;
                $recipients[$email] = $name ?? $email;
            } else {
                $recipients[$address] = $address;
            }
        }

        return $recipients;
    }
}
