<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendItemsSyncService
{
    protected string $tenantId;

    protected string $clientId;

    protected string $clientSecret;

    protected string $mailboxEmail;

    protected Client $httpClient;

    protected string $baseUrl = 'https://graph.microsoft.com/v1.0';

    public function __construct()
    {
        $this->tenantId = config('services.microsoft.tenant_id');
        $this->clientId = config('services.microsoft.client_id');
        $this->clientSecret = config('services.microsoft.client_secret');
        $this->mailboxEmail = config('services.microsoft.mailbox_email', 'notificaciones@meditecpty.com');

        $this->httpClient = new Client([
            'timeout' => 30,
            'verify' => true,
        ]);
    }

    /**
     * Obtiene el token de acceso
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('microsoft_graph_sent_items_token', 3500, function () {
            try {
                $response = $this->httpClient->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
                    'form_params' => [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'scope' => 'https://graph.microsoft.com/.default',
                        'grant_type' => 'client_credentials',
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                return $data['access_token'];
            } catch (Exception $e) {
                Log::error('Error obteniendo token para Sent Items Sync: '.$e->getMessage());
                throw new Exception('No se pudo autenticar: '.$e->getMessage());
            }
        });
    }

    /**
     * Guarda una copia del correo en la carpeta Sent Items
     *
     * @param  array  $recipients  Array de destinatarios ['email' => 'name']
     * @param  string  $subject  Asunto del correo
     * @param  string  $htmlBody  Contenido HTML del correo
     * @param  array  $ccRecipients  Array de destinatarios CC (opcional)
     * @param  array  $bccRecipients  Array de destinatarios BCC (opcional)
     */
    public function saveSentEmail(
        array $recipients,
        string $subject,
        string $htmlBody,
        array $ccRecipients = [],
        array $bccRecipients = []
    ): bool {
        try {
            $token = $this->getAccessToken();

            // Construir los destinatarios en formato Graph API
            $toRecipients = $this->buildRecipients($recipients);
            $ccRecipientsFormatted = $this->buildRecipients($ccRecipients);
            $bccRecipientsFormatted = $this->buildRecipients($bccRecipients);

            // Construir el mensaje
            $message = [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $htmlBody,
                ],
                'toRecipients' => $toRecipients,
                'from' => [
                    'emailAddress' => [
                        'address' => $this->mailboxEmail,
                        'name' => config('app.name', 'SAMI'),
                    ],
                ],
            ];

            if (! empty($ccRecipientsFormatted)) {
                $message['ccRecipients'] = $ccRecipientsFormatted;
            }

            if (! empty($bccRecipientsFormatted)) {
                $message['bccRecipients'] = $bccRecipientsFormatted;
            }

            // Endpoint para crear mensaje en Sent Items
            $url = "{$this->baseUrl}/users/{$this->mailboxEmail}/mailFolders/SentItems/messages";

            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $message,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info('Correo guardado en Sent Items', [
                'message_id' => $responseData['id'] ?? null,
                'subject' => $subject,
                'recipients' => count($recipients),
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Error guardando correo en Sent Items: '.$e->getMessage(), [
                'subject' => $subject,
                'recipients' => $recipients,
                'exception' => $e->getMessage(),
            ]);

            // No lanzamos excepción para no afectar el envío del correo
            return false;
        }
    }

    /**
     * Construye el array de destinatarios en formato Graph API
     */
    protected function buildRecipients(array $recipients): array
    {
        $formatted = [];

        foreach ($recipients as $email => $name) {
            // Si el array tiene índices numéricos, el valor es el email
            if (is_numeric($email)) {
                $email = $name;
                $name = null;
            }

            $formatted[] = [
                'emailAddress' => [
                    'address' => $email,
                    'name' => $name ?? $email,
                ],
            ];
        }

        return $formatted;
    }

    /**
     * Guarda un correo desde un objeto Swift_Message o Symfony\Component\Mime\Email
     */
    public function saveFromMailMessage($message): bool
    {
        try {
            // Extraer información del mensaje
            $subject = method_exists($message, 'getSubject') ? $message->getSubject() : '';
            $htmlBody = method_exists($message, 'getHtmlBody') ? $message->getHtmlBody() : '';

            // Si no hay HTML body, intentar con texto plano
            if (empty($htmlBody) && method_exists($message, 'getTextBody')) {
                $htmlBody = '<pre>'.htmlspecialchars($message->getTextBody()).'</pre>';
            }

            // Extraer destinatarios
            $to = [];
            $cc = [];
            $bcc = [];

            if (method_exists($message, 'getTo')) {
                $toAddresses = $message->getTo();
                foreach ($toAddresses as $address) {
                    if (is_object($address)) {
                        $to[$address->getAddress()] = $address->getName() ?? $address->getAddress();
                    } else {
                        $to[$address] = $address;
                    }
                }
            }

            if (method_exists($message, 'getCc')) {
                $ccAddresses = $message->getCc() ?? [];
                foreach ($ccAddresses as $address) {
                    if (is_object($address)) {
                        $cc[$address->getAddress()] = $address->getName() ?? $address->getAddress();
                    } else {
                        $cc[$address] = $address;
                    }
                }
            }

            if (method_exists($message, 'getBcc')) {
                $bccAddresses = $message->getBcc() ?? [];
                foreach ($bccAddresses as $address) {
                    if (is_object($address)) {
                        $bcc[$address->getAddress()] = $address->getName() ?? $address->getAddress();
                    } else {
                        $bcc[$address] = $address;
                    }
                }
            }

            return $this->saveSentEmail($to, $subject, $htmlBody, $cc, $bcc);
        } catch (Exception $e) {
            Log::error('Error procesando mensaje para Sent Items: '.$e->getMessage());

            return false;
        }
    }
}
