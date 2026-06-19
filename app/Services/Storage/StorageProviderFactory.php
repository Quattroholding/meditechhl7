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
            $accessToken = $config['dropbox_access_token'] ?? null;

            if (! $accessToken) {
                Log::warning('Dropbox access token not found in configuration');

                return null;
            }

            $decryptedToken = Crypt::decryptString($accessToken);

            return new DropboxStorageProvider($decryptedToken);
        } catch (\Exception $e) {
            Log::error('Error creating Dropbox provider', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
