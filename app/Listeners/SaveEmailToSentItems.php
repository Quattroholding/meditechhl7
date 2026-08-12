<?php

namespace App\Listeners;

use App\Services\SendItemsSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SaveEmailToSentItems implements ShouldQueue
{
    use InteractsWithQueue;

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
        // Solo guardar si está habilitado en config
        if (! config('services.microsoft.save_to_sent_items', true)) {
            return;
        }

        try {
            // Obtener el mensaje enviado
            $message = $event->sent->getOriginalMessage();

            // Extraer información del mensaje
            $subject = $message->getSubject() ?? '(Sin asunto)';
            $to = $this->extractRecipients($message->getTo());

            // Generar un ID único para este mensaje basado en su contenido
            $messageHash = md5($subject.serialize($to).now()->format('Y-m-d H:i:s'));
            $lockKey = "save-sent-item:{$messageHash}";

            // Usar un lock atómico para prevenir duplicados con múltiples workers
            $lock = Cache::lock($lockKey, 300);

            if (! $lock->get()) {
                Log::info('Correo ya está siendo procesado por otro worker, saltando duplicado', [
                    'subject' => $subject,
                    'lock_key' => $lockKey,
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

                // Guardar en Sent Items
                $syncService->saveSentEmail($to, $subject, $htmlBody, $cc, $bcc);

                Log::info('Correo sincronizado a Sent Items', [
                    'subject' => $subject,
                    'recipients_count' => count($to),
                    'lock_key' => $lockKey,
                ]);
            } finally {
                // Liberar el lock
                $lock->release();
            }
        } catch (\Exception $e) {
            // Loguear el error pero no fallar el envío del correo
            Log::error('Error guardando correo en Sent Items: '.$e->getMessage(), [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
