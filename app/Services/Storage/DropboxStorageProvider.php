<?php

namespace App\Services\Storage;

use App\Contracts\Storage\ExternalStorageProvider;
use App\Models\ClientPreference;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxStorageProvider implements ExternalStorageProvider
{
    protected string $accessToken;

    protected ?string $refreshToken;

    protected ?string $expiresAt;

    protected ?int $clientId;

    public function __construct(
        string $accessToken,
        ?string $refreshToken = null,
        ?string $expiresAt = null,
        ?int $clientId = null
    ) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;
        $this->clientId = $clientId;

        // Check if token needs refresh on initialization
        if ($this->shouldRefreshToken()) {
            $this->refreshAccessToken();
        }
    }

    /**
     * Check if access token needs to be refreshed.
     */
    protected function shouldRefreshToken(): bool
    {
        if (! $this->refreshToken || ! $this->expiresAt) {
            return false;
        }

        // Refresh if token expires in less than 5 minutes
        return now()->addMinutes(5)->greaterThan($this->expiresAt);
    }

    /**
     * Refresh the access token using refresh token.
     */
    protected function refreshAccessToken(): void
    {
        if (! $this->refreshToken) {
            throw new \Exception('No refresh token available');
        }

        try {
            $response = Http::asForm()->post('https://api.dropboxapi.com/oauth2/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
                'client_id' => config('services.dropbox.app_key'),
                'client_secret' => config('services.dropbox.app_secret'),
            ]);

            if (! $response->successful()) {
                throw new \Exception('Failed to refresh token: '.$response->body());
            }

            $data = $response->json();

            // Update access token
            $this->accessToken = $data['access_token'];
            $this->expiresAt = now()->addSeconds($data['expires_in'])->toDateTimeString();

            // Save new token to database if we have client_id
            if ($this->clientId) {
                $config = ClientPreference::getExternalStorageConfig($this->clientId);

                if ($config) {
                    $config['access_token'] = Crypt::encryptString($this->accessToken);
                    $config['expires_at'] = $this->expiresAt;

                    ClientPreference::setExternalStorageConfig($this->clientId, $config);

                    Log::info('Dropbox access token refreshed automatically', [
                        'client_id' => $this->clientId,
                        'expires_at' => $this->expiresAt,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to refresh Dropbox access token', [
                'client_id' => $this->clientId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle expired token error and retry operation.
     */
    protected function handleExpiredToken(\Exception $e, callable $operation): mixed
    {
        // Check if error is due to expired token
        if (str_contains($e->getMessage(), 'expired_access_token') || str_contains($e->getMessage(), '401')) {
            if ($this->refreshToken) {
                try {
                    $this->refreshAccessToken();

                    // Retry the operation with new token
                    return $operation();
                } catch (\Exception $refreshError) {
                    Log::error('Failed to refresh token and retry operation', [
                        'error' => $refreshError->getMessage(),
                    ]);
                    throw $refreshError;
                }
            }
        }

        throw $e;
    }

    protected function getFilesystem(): Filesystem
    {
        $client = new DropboxClient($this->accessToken);
        $adapter = new DropboxAdapter($client);

        return new Filesystem($adapter);
    }

    public function upload(UploadedFile $file, string $path): array
    {
        $operation = function () use ($file, $path) {
            $filesystem = $this->getFilesystem();
            $contents = file_get_contents($file->getRealPath());

            $filesystem->write($path, $contents);

            $client = new DropboxClient($this->accessToken);
            $metadata = $client->getMetadata($path);

            Log::info('File uploaded to Dropbox', [
                'path' => $path,
                'size' => $file->getSize(),
                'dropbox_id' => $metadata['id'] ?? null,
            ]);

            return [
                'external_id' => $metadata['id'] ?? $path,
                'path' => $path,
                'size' => $metadata['size'] ?? $file->getSize(),
                'metadata' => $metadata,
            ];
        };

        try {
            return $operation();
        } catch (\Exception $e) {
            Log::error('Error uploading file to Dropbox', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            // Try to refresh token and retry
            return $this->handleExpiredToken($e, $operation);
        }
    }

    public function delete(string $pathOrId): bool
    {
        $operation = function () use ($pathOrId) {
            $filesystem = $this->getFilesystem();
            $filesystem->delete($pathOrId);

            Log::info('File deleted from Dropbox', ['path' => $pathOrId]);

            return true;
        };

        try {
            return $operation();
        } catch (\Exception $e) {
            Log::error('Error deleting file from Dropbox', [
                'path_or_id' => $pathOrId,
                'error' => $e->getMessage(),
            ]);

            // Try to refresh token and retry
            return $this->handleExpiredToken($e, $operation);
        }
    }

    public function getTemporaryUrl(string $externalId, int $expirationMinutes = 60): string
    {
        $operation = function () use ($externalId) {
            $client = new DropboxClient($this->accessToken);

            // First, try to get existing shared links
            try {
                $links = $client->listSharedLinks($externalId);
                // Note: listSharedLinks returns array directly, not wrapped in 'links' key
                if (!empty($links) && is_array($links)) {
                    $url = $links[0]['url'] ?? null;
                    if ($url) {
                        // Convert to raw link for direct viewing
                        $url = str_replace('dl=0', 'raw=1', $url);
                        return $url;
                    }
                }
            } catch (\Exception $e) {
                Log::debug('No existing shared links found', [
                    'external_id' => $externalId,
                    'error' => $e->getMessage(),
                ]);
            }

            // If no existing link, try to create a new one
            try {
                $result = $client->createSharedLinkWithSettings($externalId, [
                    'requested_visibility' => 'public',
                ]);
                $url = $result['url'] ?? null;
                if ($url) {
                    // Convert to raw link for direct viewing
                    $url = str_replace('dl=0', 'raw=1', $url);
                    return $url;
                }
            } catch (\Exception $e) {
                // If create fails because link already exists, try listing again
                if (str_contains($e->getMessage(), 'shared_link_already_exists')) {
                    try {
                        $links = $client->listSharedLinks($externalId);
                        if (!empty($links) && is_array($links)) {
                            $url = $links[0]['url'] ?? null;
                            if ($url) {
                                $url = str_replace('dl=0', 'raw=1', $url);
                                return $url;
                            }
                        }
                    } catch (\Exception $e2) {
                        Log::error('Failed to list existing links after create error', [
                            'external_id' => $externalId,
                            'error' => $e2->getMessage(),
                        ]);
                    }
                }

                // Log the error but continue to fallback
                Log::warning('createSharedLinkWithSettings failed', [
                    'external_id' => $externalId,
                    'error' => $e->getMessage(),
                ]);
            }

            // Final fallback: try getTemporaryLink (requires files.content.read)
            try {
                $result = $client->getTemporaryLink($externalId);
                return is_array($result) ? $result['link'] : $result;
            } catch (\Exception $e) {
                Log::error('getTemporaryLink also failed', [
                    'external_id' => $externalId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        };

        try {
            return $operation();
        } catch (\Exception $e) {
            Log::error('Error getting temporary URL from Dropbox', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            // Try to refresh token and retry
            try {
                return $this->handleExpiredToken($e, $operation);
            } catch (\Exception $e2) {
                Log::error('Failed even after token refresh', [
                    'external_id' => $externalId,
                    'error' => $e2->getMessage(),
                ]);
                throw $e2;
            }
        }
    }

    public function getShareableUrl(string $externalId): string
    {
        $operation = function () use ($externalId) {
            $client = new DropboxClient($this->accessToken);

            try {
                $result = $client->createSharedLinkWithSettings($externalId);
            } catch (\Exception $e) {
                $links = $client->listSharedLinks($externalId);
                if (! empty($links['links'])) {
                    return $links['links'][0]['url'];
                }
                throw $e;
            }

            return $result['url'];
        };

        try {
            return $operation();
        } catch (\Exception $e) {
            Log::error('Error getting shareable URL from Dropbox', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            // Try to refresh token and retry
            return $this->handleExpiredToken($e, $operation);
        }
    }

    public function exists(string $externalId): bool
    {
        try {
            $filesystem = $this->getFilesystem();

            return $filesystem->fileExists($externalId);
        } catch (\Exception $e) {
            // Try to refresh and retry
            try {
                return $this->handleExpiredToken($e, fn () => $this->getFilesystem()->fileExists($externalId));
            } catch (\Exception $retryException) {
                return false;
            }
        }
    }

    public function size(string $externalId): int
    {
        $operation = function () use ($externalId) {
            $filesystem = $this->getFilesystem();

            return $filesystem->fileSize($externalId);
        };

        try {
            return $operation();
        } catch (\Exception $e) {
            Log::error('Error getting file size from Dropbox', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            // Try to refresh token and retry
            try {
                return $this->handleExpiredToken($e, $operation);
            } catch (\Exception $retryException) {
                return 0;
            }
        }
    }

    public function validateCredentials(): bool
    {
        try {
            $client = new DropboxClient($this->accessToken);
            $client->rpcEndpointRequest('users/get_current_account');

            return true;
        } catch (\Exception $e) {
            Log::error('Dropbox credentials validation failed', [
                'error' => $e->getMessage(),
            ]);

            // Try to refresh token and retry validation
            if ($this->refreshToken) {
                try {
                    $this->refreshAccessToken();
                    $client = new DropboxClient($this->accessToken);
                    $client->rpcEndpointRequest('users/get_current_account');

                    return true;
                } catch (\Exception $retryException) {
                    return false;
                }
            }

            return false;
        }
    }

    public function getProviderName(): string
    {
        return 'Dropbox';
    }
}
