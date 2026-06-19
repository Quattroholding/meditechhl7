<?php

namespace App\Services\Storage;

use App\Contracts\Storage\ExternalStorageProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxStorageProvider implements ExternalStorageProvider
{
    public function __construct(
        protected string $accessToken
    ) {}

    protected function getFilesystem(): Filesystem
    {
        $client = new DropboxClient($this->accessToken);
        $adapter = new DropboxAdapter($client);

        return new Filesystem($adapter);
    }

    public function upload(UploadedFile $file, string $path): array
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error uploading file to Dropbox', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function delete(string $pathOrId): bool
    {
        try {
            $filesystem = $this->getFilesystem();

            // Dropbox filesystem solo acepta paths, no IDs
            // Si recibimos un ID, intentar eliminar por path directamente
            $filesystem->delete($pathOrId);

            Log::info('File deleted from Dropbox', ['path' => $pathOrId]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting file from Dropbox', [
                'path_or_id' => $pathOrId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getTemporaryUrl(string $externalId, int $expirationMinutes = 60): string
    {
        try {
            $client = new DropboxClient($this->accessToken);
            $result = $client->getTemporaryLink($externalId);

            // getTemporaryLink puede devolver string o array dependiendo de la versión
            return is_array($result) ? $result['link'] : $result;
        } catch (\Exception $e) {
            Log::error('Error getting temporary URL from Dropbox', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function getShareableUrl(string $externalId): string
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error getting shareable URL from Dropbox', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function exists(string $externalId): bool
    {
        try {
            $filesystem = $this->getFilesystem();

            return $filesystem->fileExists($externalId);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function size(string $externalId): int
    {
        try {
            $filesystem = $this->getFilesystem();

            return $filesystem->fileSize($externalId);
        } catch (\Exception $e) {
            Log::error('Error getting file size from Dropbox', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            return 0;
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

            return false;
        }
    }

    public function getProviderName(): string
    {
        return 'Dropbox';
    }
}
