<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MicrosoftGraphService
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
     * Obtiene el token de acceso usando autenticación de aplicación (Client Credentials)
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('microsoft_graph_token', 3500, function () {
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
                Log::error('Error obteniendo token de Microsoft Graph: '.$e->getMessage());
                throw new Exception('No se pudo autenticar con Microsoft Graph: '.$e->getMessage());
            }
        });
    }

    /**
     * Obtiene los correos enviados desde una carpeta específica
     *
     * @param  int  $limit  Cantidad de correos a obtener (máximo 999)
     * @param  int  $skip  Cantidad de correos a saltar para paginación
     * @return array Arreglo con los correos y metadata
     */
    public function getSentEmails(int $limit = 50, int $skip = 0): array
    {
        try {
            $token = $this->getAccessToken();

            // Construir la URL base
            $url = "{$this->baseUrl}/users/{$this->mailboxEmail}/mailFolders/SentItems/messages";

            // Construir los parámetros de consulta correctamente
            $queryParams = [
                '$top' => $limit,
                '$skip' => $skip,
                '$orderby' => 'sentDateTime desc',
                '$select' => 'id,subject,from,toRecipients,ccRecipients,sentDateTime,bodyPreview,hasAttachments,internetMessageId',
            ];

            $url .= '?'.http_build_query($queryParams);

            $response = $this->httpClient->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'messages' => $data['value'] ?? [],
                'total_count' => $data['@odata.count'] ?? count($data['value'] ?? []),
                'has_more' => isset($data['@odata.nextLink']),
            ];
        } catch (Exception $e) {
            Log::error('Error obteniendo correos de Microsoft Graph: '.$e->getMessage());

            return [
                'success' => false,
                'messages' => [],
                'total_count' => 0,
                'has_more' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtiene los detalles completos de un correo específico
     */
    public function getEmailDetails(string $messageId): ?array
    {
        try {
            $token = $this->getAccessToken();

            $url = "{$this->baseUrl}/users/{$this->mailboxEmail}/messages/{$messageId}";

            $queryParams = [
                '$select' => 'id,subject,from,toRecipients,ccRecipients,bccRecipients,sentDateTime,receivedDateTime,body,bodyPreview,hasAttachments,internetMessageId,conversationId',
            ];

            $url .= '?'.http_build_query($queryParams);

            $response = $this->httpClient->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error("Error obteniendo detalles del correo {$messageId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Busca correos por criterios específicos
     *
     * @param  string  $searchQuery  Consulta de búsqueda (ej: "subject:factura")
     */
    public function searchSentEmails(string $searchQuery, int $limit = 50): array
    {
        try {
            $token = $this->getAccessToken();

            $url = "{$this->baseUrl}/users/{$this->mailboxEmail}/mailFolders/SentItems/messages";

            $queryParams = [
                '$search' => "\"{$searchQuery}\"",
                '$top' => $limit,
                '$orderby' => 'sentDateTime desc',
            ];

            $url .= '?'.http_build_query($queryParams);

            $response = $this->httpClient->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'messages' => $data['value'] ?? [],
                'total_count' => count($data['value'] ?? []),
            ];
        } catch (Exception $e) {
            Log::error('Error buscando correos: '.$e->getMessage());

            return [
                'success' => false,
                'messages' => [],
                'total_count' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verifica el estado de entrega del correo (requiere permisos adicionales)
     */
    public function getMessageTrackingInfo(string $internetMessageId): ?array
    {
        try {
            $token = $this->getAccessToken();

            // Esta funcionalidad requiere permisos de Message Trace en Exchange Online
            // Nota: No todos los tenants tienen esta API habilitada
            $url = "{$this->baseUrl}/users/{$this->mailboxEmail}/messages";

            $queryParams = [
                '$filter' => "internetMessageId eq '{$internetMessageId}'",
            ];

            $url .= '?'.http_build_query($queryParams);

            $response = $this->httpClient->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return $data['value'][0] ?? null;
        } catch (Exception $e) {
            Log::error("Error obteniendo tracking del mensaje {$internetMessageId}: ".$e->getMessage());

            return null;
        }
    }
}
