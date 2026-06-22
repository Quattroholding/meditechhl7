<?php

namespace App\Services\Storage;

use App\Contracts\Storage\ExternalStorageProvider;
use App\Enums\PreferenceType;
use App\Models\ClientPreference;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class StorageProviderFactory
{
    /**
     * Create a storage provider instance for the given client.
     */
    public static function make(int $clientId): ?ExternalStorageProvider
    {
        $config = static::getConfig($clientId);

        if (! $config || ! ($config['enabled'] ?? false)) {
            return null;
        }

        $provider = $config['provider'] ?? null;

        // Add client_id to config for token refresh
        $config['client_id'] = $clientId;

        return match ($provider) {
            'dropbox' => static::makeDropboxProvider($config),
            default => null,
        };
    }

    /**
     * Get the disk name for the given client.
     */
    public static function getDiskName(int $clientId): string
    {
        $config = static::getConfig($clientId);

        if (! $config || ! ($config['enabled'] ?? false)) {
            return 'public';
        }

        return "client_{$clientId}_external";
    }

    /**
     * Get the storage configuration for a client.
     */
    protected static function getConfig(int $clientId): ?array
    {
        $config = ClientPreference::get($clientId, PreferenceType::EXTERNAL_STORAGE, 'config');

        if (! $config) {
            return null;
        }

        return $config;
    }

    /**
     * Create a Dropbox provider instance.
     */
    protected static function makeDropboxProvider(array $config): ?ExternalStorageProvider
    {
        try {
            // Support both old (dropbox_access_token) and new (access_token) config keys
            $accessToken = $config['access_token'] ?? $config['dropbox_access_token'] ?? null;

            if (! $accessToken) {
                Log::warning('Dropbox access token not found in configuration');

                return null;
            }

            $decryptedAccessToken = Crypt::decryptString($accessToken);

            // OAuth tokens
            $refreshToken = null;
            $expiresAt = null;

            if (isset($config['refresh_token'])) {
                try {
                    $refreshToken = Crypt::decryptString($config['refresh_token']);
                } catch (\Exception $e) {
                    Log::warning('Failed to decrypt refresh token', ['error' => $e->getMessage()]);
                }
            }

            if (isset($config['expires_at'])) {
                $expiresAt = $config['expires_at'];
            }

            // Get client_id from config metadata if available
            $clientId = $config['client_id'] ?? null;

            return new DropboxStorageProvider(
                $decryptedAccessToken,
                $refreshToken,
                $expiresAt,
                $clientId
            );
        } catch (\Exception $e) {
            Log::error('Error creating Dropbox provider', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
