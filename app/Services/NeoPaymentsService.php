<?php

namespace App\Services;

use App\Enums\NeoPaymentStatus;
use App\Models\Client;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NeoPaymentsService
{
    private string $host;

    private string $clientId;

    private string $clientSecret;

    private int $retryAttempts;

    private ?string $accessToken = null;

    public function __construct()
    {
        $this->host = rtrim(config('services.neopayments.host'), '/');
        $this->clientId = config('services.neopayments.client_id');
        $this->clientSecret = config('services.neopayments.client_secret');
        $this->retryAttempts = config('services.neopayments.retry_attempts', 2);

        // Validate credentials are configured
        if (empty($this->clientId) || empty($this->clientSecret)) {
            throw new \RuntimeException(
                'NeoPayments credentials not configured. '.
                'Set NEOPAYMENTS_CLIENT_ID and NEOPAYMENTS_CLIENT_SECRET in .env file.'
            );
        }
    }

    private function authenticate(): string
    {
        $cacheKey = 'neopayments_access_token';

        return Cache::remember($cacheKey, now()->addHours(23), function () {
            try {
                $response = Http::asForm()->post($this->host.'/oauth/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

                if ($response->failed()) {
                    Log::error('NeoPayments authentication failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    throw new \Exception('NeoPayments authentication failed: '.$response->body());
                }

                $data = $response->json();

                Log::info('NeoPayments authentication successful');

                return $data['access_token'];
            } catch (\Exception $e) {
                Log::error('NeoPayments authentication exception', [
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    private function httpClient(): PendingRequest
    {
        if (! $this->accessToken) {
            $this->accessToken = $this->authenticate();
        }

        return Http::withToken($this->accessToken)
            ->acceptJson()
            ->timeout(30);
    }

    /**
     * Refresh access token by clearing cache and re-authenticating
     */
    private function refreshAccessToken(): void
    {
        Log::info('NeoPayments: Refreshing expired access token');

        Cache::forget('neopayments_access_token');
        $this->accessToken = null;
        $this->accessToken = $this->authenticate();
    }

    /**
     * Make an authenticated HTTP request with automatic token refresh on 401
     */
    private function makeAuthenticatedRequest(callable $requestCallback, int $retryCount = 0): mixed
    {
        $response = $requestCallback($this->httpClient());

        // If we get a 401 Unauthenticated error and haven't retried yet, refresh token and retry
        if ($response->status() === 401 && $retryCount === 0) {
            Log::warning('NeoPayments: Received 401 Unauthenticated, refreshing token and retrying', [
                'body' => $response->body(),
            ]);

            $this->refreshAccessToken();

            // Retry the request with the new token
            return $this->makeAuthenticatedRequest($requestCallback, $retryCount + 1);
        }

        return $response;
    }

    public function createCustomer(Client $client): array
    {
        try {
            $response = $this->makeAuthenticatedRequest(function ($http) use ($client) {
                return $http->post($this->host.'/api/v2/customers', [
                    'name' => explode(' ', $client->name)[0] ?? $client->name,
                    'first_surname' => explode(' ', $client->name)[1] ?? '',
                    'doc_id_type' => 'C',
                    'doc_id' => $client->email,
                    'customer_type' => 'legal_person',
                    'status' => 'active',
                ]);
            });

            if ($response->failed()) {
                Log::error('NeoPayments customer creation failed', [
                    'client_id' => $client->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Failed to create NeoPayments customer: '.$response->body());
            }

            $data = $response->json();

            Log::info('NeoPayments customer created', [
                'client_id' => $client->id,
                'customer_id' => $data['data']['id'] ?? null,
            ]);

            return $data['data'] ?? $data;
        } catch (\Exception $e) {
            Log::error('NeoPayments customer creation exception', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function addCard(string $customerId, array $cardData): array
    {
        try {
            $cardNumber = preg_replace('/\s+/', '', $cardData['card_number']);
            $lastFour = substr($cardNumber, -4);

            $response = $this->makeAuthenticatedRequest(function ($http) use ($customerId, $cardData, $cardNumber) {
                return $http->post($this->host."/api/v2/customers/{$customerId}/card", [
                    'card_holder' => $cardData['card_holder'],
                    'card_number' => $cardNumber,
                    'exp_date' => $cardData['exp_date'],
                    'status' => 'active',
                ]);
            });

            if ($response->failed()) {
                Log::error('NeoPayments card tokenization failed', [
                    'customer_id' => $customerId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Failed to tokenize card: '.$response->body());
            }

            $data = $response->json();

            Log::info('NeoPayments card tokenized', [
                'customer_id' => $customerId,
                'card_id' => $cardData['id'] ?? null,
                'card_last_four' => $lastFour,
            ]);

            // Extract data from nested response structure if present
            $cardData = $data['data'] ?? $data;
            $cardData['card_last_four'] = $lastFour;

            return $cardData;
        } catch (\Exception $e) {
            Log::error('NeoPayments card tokenization exception', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function deleteCard(string $customerId, string $cardId): bool
    {
        try {
            $response = $this->makeAuthenticatedRequest(function ($http) use ($customerId, $cardId) {
                return $http->delete($this->host."/api/v2/customers/{$customerId}/card/{$cardId}");
            });

            if ($response->failed()) {
                Log::error('NeoPayments card deletion failed', [
                    'customer_id' => $customerId,
                    'card_id' => $cardId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            Log::info('NeoPayments card deleted', [
                'customer_id' => $customerId,
                'card_id' => $cardId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('NeoPayments card deletion exception', [
                'customer_id' => $customerId,
                'card_id' => $cardId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function processPayment(string $cardToken, float $amount, array $metadata = [], ?array $browserData = null): array
    {
        try {
            $amountInCents = (int) ($amount * 100);

            $payload = [
                'customer_token' => $cardToken,
                'currency_code' => 'USD',
                'amount' => $amountInCents,
                'metadatas' => $metadata,
            ];

            // Include 3DS parameters if enabled and browser data is provided
            if (config('services.neopayments.3ds_enabled', false) && $browserData) {
                $payload['3ds_params'] = $this->build3dsParams($browserData);

                // Use WEBHOOK_BASE_PATH if configured, otherwise use route helper
                $webhookBasePath = config('services.neopayments.webhook_base_path');
                $payload['webhook'] = $webhookBasePath
                    ? rtrim($webhookBasePath, '/').'/neopayments'
                    : route('webhooks.neopayments');

                // Optional: Add return URL for redirecting user after challenge
                $payload['return_url'] = $browserData['return_url'] ?? config('app.url').'/payment/result';

                Log::info('NeoPayments: 3DS enabled, including 3DS params', [
                    'has_browser_data' => ! empty($browserData),
                    'webhook' => $payload['webhook'],
                ]);
            }

            $response = $this->makeAuthenticatedRequest(function ($http) use ($payload) {
                return $http->post($this->host.'/api/v2/recurrent_payment/sale', $payload);
            });

            if ($response->failed()) {
                Log::error('NeoPayments payment processing failed', [
                    'amount' => $amount,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'metadata' => $metadata,
                    '3ds_enabled' => ! empty($payload['3ds_params']),
                ]);

                throw new \Exception('Payment processing failed: '.$response->body());
            }

            $data = $response->json();

            // Extract transaction data from nested response
            $transactionData = $data['data'] ?? $data;

            Log::info('NeoPayments payment processed', [
                'transaction_id' => $transactionData['id'] ?? $transactionData['identifier'] ?? null,
                'amount' => $amount,
                'status' => $transactionData['status'] ?? null,
                '3ds_authentication_required' => ($transactionData['status'] ?? null) === 'authenticating',
            ]);

            return $transactionData;
        } catch (\Exception $e) {
            Log::error('NeoPayments payment processing exception', [
                'amount' => $amount,
                'error' => $e->getMessage(),
                'metadata' => $metadata,
            ]);

            throw $e;
        }
    }

    /**
     * Build 3DS parameters from browser data
     */
    private function build3dsParams(array $browserData): array
    {
        // Convert java_enabled to boolean (comes as string from POST)
        $javaEnabled = false;
        if (isset($browserData['java_enabled'])) {
            $javaEnabled = filter_var($browserData['java_enabled'], FILTER_VALIDATE_BOOLEAN);
        }

        return [
            'deviceChannel' => 'browser',
            'browserIP' => $browserData['ip'] ?? '127.0.0.1',
            'browserJavaEnabled' => $javaEnabled,
            'browserJavascriptEnabled' => true,
            'browserLanguage' => $browserData['language'] ?? 'es',
            'browserColorDepth' => (int) ($browserData['color_depth'] ?? 24),
            'browserScreenHeight' => (int) ($browserData['screen_height'] ?? 1080),
            'browserScreenWidth' => (int) ($browserData['screen_width'] ?? 1920),
            'browserTZ' => (string) ($browserData['timezone_offset'] ?? '0'),
            'browserUserAgent' => $browserData['user_agent'] ?? request()->userAgent(),
            'challengeWindowSize' => (int) ($browserData['window_size'] ?? $browserData['screen_width'] ?? 1920),
        ];
    }

    public function getTransaction(string $transactionId): array
    {
        try {
            $response = $this->makeAuthenticatedRequest(function ($http) use ($transactionId) {
                return $http->get($this->host."/api/v2/transactions/{$transactionId}");
            });

            if ($response->failed()) {
                Log::error('NeoPayments transaction query failed', [
                    'transaction_id' => $transactionId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Transaction query failed: '.$response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('NeoPayments transaction query exception', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function processPaymentWithRetry(string $cardToken, float $amount, array $metadata = [], ?array $browserData = null): array
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts <= $this->retryAttempts) {
            try {
                $result = $this->processPayment($cardToken, $amount, $metadata, $browserData);

                $status = $this->mapTransactionStatus($result['status'] ?? 'FAILED');

                if ($status->isSuccess()) {
                    return $result;
                }

                // If status is "authenticating", return immediately (3DS challenge required)
                if (($result['status'] ?? null) === 'authenticating') {
                    Log::info('NeoPayments: 3DS authentication required, returning for user challenge');

                    return $result;
                }

                if ($status->isFailed() && $attempts < $this->retryAttempts) {
                    $attempts++;
                    $waitSeconds = $attempts * 30;

                    Log::warning('NeoPayments payment failed, retrying', [
                        'attempt' => $attempts,
                        'wait_seconds' => $waitSeconds,
                        'status' => $status->value,
                    ]);

                    sleep($waitSeconds);

                    continue;
                }

                return $result;
            } catch (\Exception $e) {
                $lastException = $e;
                $attempts++;

                if ($attempts <= $this->retryAttempts) {
                    $waitSeconds = $attempts * 30;

                    Log::warning('NeoPayments payment exception, retrying', [
                        'attempt' => $attempts,
                        'wait_seconds' => $waitSeconds,
                        'error' => $e->getMessage(),
                    ]);

                    sleep($waitSeconds);
                } else {
                    throw $e;
                }
            }
        }

        if ($lastException) {
            throw $lastException;
        }

        throw new \Exception('Payment failed after maximum retry attempts');
    }

    public function mapTransactionStatus(string $status): NeoPaymentStatus
    {
        return match (strtoupper($status)) {
            'PENDING' => NeoPaymentStatus::PENDING,
            'AUTHORIZED', 'APPROVED', 'SUCCESS', 'OK' => NeoPaymentStatus::AUTHORIZED,
            'DENIED', 'DECLINED' => NeoPaymentStatus::DENIED,
            'REFUSED', 'REJECTED' => NeoPaymentStatus::REFUSED,
            default => NeoPaymentStatus::FAILED,
        };
    }
}
