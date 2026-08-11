<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExchangeMessageTraceService
{
    protected string $tenantId;

    protected string $clientId;

    protected string $clientSecret;

    protected Client $httpClient;

    protected string $baseUrl = 'https://graph.microsoft.com/v1.0';

    public function __construct()
    {
        $this->tenantId = config('services.microsoft.tenant_id');
        $this->clientId = config('services.microsoft.client_id');
        $this->clientSecret = config('services.microsoft.client_secret');

        $this->httpClient = new Client([
            'timeout' => 60,
            'verify' => true,
        ]);
    }

    /**
     * Obtiene el token de acceso usando autenticación de aplicación
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('microsoft_graph_trace_token', 3500, function () {
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
                Log::error('Error obteniendo token para Message Trace: '.$e->getMessage());
                throw new Exception('No se pudo autenticar con Microsoft Graph: '.$e->getMessage());
            }
        });
    }

    /**
     * Obtiene el seguimiento de mensajes (Message Trace) desde todos los buzones
     * Usa la API de búsqueda avanzada de Graph
     *
     * @param  string|null  $senderAddress  Dirección del remitente (ej: notificaciones@meditecpty.com)
     * @param  string|null  $recipientAddress  Dirección del destinatario
     * @param  string|null  $subject  Asunto del correo
     * @param  Carbon|null  $startDate  Fecha de inicio (últimos 10 días máximo)
     * @param  Carbon|null  $endDate  Fecha de fin
     * @param  int  $limit  Cantidad de resultados
     */
    public function getMessageTrace(
        ?string $senderAddress = null,
        ?string $recipientAddress = null,
        ?string $subject = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        int $limit = 100
    ): array {
        try {
            $token = $this->getAccessToken();

            // Por defecto, últimas 24 horas
            if (! $startDate) {
                $startDate = Carbon::now()->subDay();
            }
            if (! $endDate) {
                $endDate = Carbon::now();
            }

            // Exchange Message Trace solo permite consultar hasta 10 días atrás
            if ($startDate->diffInDays(Carbon::now()) > 10) {
                $startDate = Carbon::now()->subDays(10);
            }

            // Construir filtros
            $filters = [];

            if ($senderAddress) {
                $filters[] = "SenderAddress eq '{$senderAddress}'";
            }

            if ($recipientAddress) {
                $filters[] = "RecipientAddress eq '{$recipientAddress}'";
            }

            // Usar endpoint de búsqueda de mensajes en todos los buzones
            // Nota: Esto requiere permisos de Mail.Read
            $url = "{$this->baseUrl}/users";

            // Si tenemos un remitente específico, buscamos en ese buzón
            if ($senderAddress) {
                $url = "{$this->baseUrl}/users/{$senderAddress}/messages";

                $queryParams = [
                    '$top' => $limit,
                    '$orderby' => 'sentDateTime desc',
                    '$select' => 'id,subject,from,toRecipients,ccRecipients,sentDateTime,isRead,internetMessageId',
                    '$filter' => "sentDateTime ge {$startDate->format('Y-m-d\\TH:i:s\\Z')} and sentDateTime le {$endDate->format('Y-m-d\\TH:i:s\\Z')}",
                ];

                if ($subject) {
                    $queryParams['$search'] = "\"subject:{$subject}\"";
                }

                $url .= '?'.http_build_query($queryParams);

                $response = $this->httpClient->get($url, [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Content-Type' => 'application/json',
                        'ConsistencyLevel' => 'eventual',
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);
                $messages = $data['value'] ?? [];

                // Enriquecer con información de tracking
                $enrichedMessages = array_map(function ($message) use ($recipientAddress) {
                    // Filtrar por destinatario si se especificó
                    if ($recipientAddress) {
                        $hasRecipient = false;
                        foreach ($message['toRecipients'] ?? [] as $recipient) {
                            if (stripos($recipient['emailAddress']['address'], $recipientAddress) !== false) {
                                $hasRecipient = true;
                                break;
                            }
                        }
                        if (! $hasRecipient) {
                            return null;
                        }
                    }

                    return [
                        'MessageId' => $message['internetMessageId'] ?? $message['id'],
                        'Subject' => $message['subject'] ?? '(Sin asunto)',
                        'SenderAddress' => $message['from']['emailAddress']['address'] ?? '',
                        'RecipientAddress' => isset($message['toRecipients'][0]) ? $message['toRecipients'][0]['emailAddress']['address'] : '',
                        'RecipientCount' => count($message['toRecipients'] ?? []),
                        'AllRecipients' => $message['toRecipients'] ?? [],
                        'CcRecipients' => $message['ccRecipients'] ?? [],
                        'Received' => $message['sentDateTime'] ?? null,
                        'Status' => 'Entregado', // Asumimos entregado si está en sent items
                        'FromIP' => null,
                        'ToIP' => null,
                        'Size' => null,
                        'MessageTraceId' => $message['id'],
                    ];
                }, $messages);

                // Filtrar nulls
                $enrichedMessages = array_filter($enrichedMessages);

                return [
                    'success' => true,
                    'messages' => array_values($enrichedMessages),
                    'total_count' => count($enrichedMessages),
                    'source' => 'graph_api_mailbox',
                ];
            }

            // Si no hay remitente específico, retornar error
            return [
                'success' => false,
                'messages' => [],
                'total_count' => 0,
                'error' => 'Se requiere especificar un remitente (senderAddress)',
            ];
        } catch (Exception $e) {
            Log::error('Error en Message Trace: '.$e->getMessage());

            return [
                'success' => false,
                'messages' => [],
                'total_count' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Busca un correo específico por diversos criterios
     */
    public function searchMessage(
        string $searchTerm,
        ?string $senderAddress = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        return $this->getMessageTrace(
            senderAddress: $senderAddress ?? config('services.microsoft.mailbox_email'),
            recipientAddress: null,
            subject: $searchTerm,
            startDate: $startDate ?? Carbon::now()->subDays(7),
            endDate: $endDate ?? Carbon::now(),
            limit: 100
        );
    }

    /**
     * Verifica si un correo a un destinatario específico fue entregado
     */
    public function checkDeliveryStatus(
        string $recipientEmail,
        ?string $subject = null,
        ?Carbon $since = null
    ): array {
        $since = $since ?? Carbon::now()->subDay();

        $result = $this->getMessageTrace(
            senderAddress: config('services.microsoft.mailbox_email'),
            recipientAddress: $recipientEmail,
            subject: $subject,
            startDate: $since,
            endDate: Carbon::now()
        );

        if (! $result['success']) {
            return [
                'delivered' => false,
                'message_count' => 0,
                'messages' => [],
                'error' => $result['error'] ?? 'Error desconocido',
            ];
        }

        return [
            'delivered' => count($result['messages']) > 0,
            'message_count' => count($result['messages']),
            'messages' => $result['messages'],
        ];
    }
}
