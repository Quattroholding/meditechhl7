<?php

namespace App\Contracts\Storage;

use Illuminate\Http\UploadedFile;

interface ExternalStorageProvider
{
    /**
     * Upload a file to external storage.
     */
    public function upload(UploadedFile $file, string $path): array;

    /**
     * Delete a file from external storage.
     */
    public function delete(string $externalId): bool;

    /**
     * Get a temporary URL for a file (expires after specified minutes).
     */
    public function getTemporaryUrl(string $externalId, int $expirationMinutes = 60): string;

    /**
     * Get a shareable URL for a file.
     */
    public function getShareableUrl(string $externalId): string;

    /**
     * Check if a file exists in external storage.
     */
    public function exists(string $externalId): bool;

    /**
     * Get the size of a file in bytes.
     */
    public function size(string $externalId): int;

    /**
     * Validate provider credentials.
     */
    public function validateCredentials(): bool;

    /**
     * Get the provider name.
     */
    public function getProviderName(): string;
}
