<?php

namespace App\Services;

use App\Contracts\Storage\ExternalStorageProvider;
use App\Models\Encounter;
use App\Models\Media;
use App\Services\Storage\StorageProviderFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaStorageService
{
    /**
     * Store consultation media file.
     */
    public function storeConsultationMedia(
        UploadedFile $file,
        Encounter $encounter,
        array $attributes = []
    ): Media {
        // Get client_id through appointment relationship
        $clientId = $encounter->appointment?->client_id ?? auth()->user()?->default_client_id;
        $provider = StorageProviderFactory::make($clientId);

        if (! $provider) {
            throw new \Exception('No hay almacenamiento externo configurado. Debe configurar Dropbox u otro proveedor antes de subir archivos.');
        }

        return $this->storeExternally($file, $encounter, $provider, $attributes);
    }

    /**
     * Store file in external storage.
     */
    protected function storeExternally(
        UploadedFile $file,
        Encounter $encounter,
        ExternalStorageProvider $provider,
        array $attributes
    ): Media {
        $path = $this->generateExternalPath($encounter, $file);

        $uploadResult = $provider->upload($file, $path);

        $clientId = $encounter->appointment?->client_id ?? auth()->user()?->default_client_id;

        $media = Media::create([
            'patient_id' => $encounter->patient_id,
            'encounter_id' => $encounter->id,
            'practitioner_id' => Auth::id(),
            'storage_disk' => StorageProviderFactory::getDiskName($clientId),
            'external_id' => $uploadResult['external_id'],
            'external_metadata' => $uploadResult['metadata'] ?? null,
            'file_path' => $uploadResult['path'],
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'content_type' => $file->getMimeType(),
            'type' => $attributes['type'] ?? $this->guessType($file),
            'modality' => $attributes['modality'] ?? null,
            'note' => $attributes['note'] ?? null,
            'status' => 'completed',
            'subject_type' => null,
            'subject_id' => null,
        ]);

        Log::info('Media stored externally', [
            'media_id' => $media->id,
            'provider' => $provider->getProviderName(),
            'external_id' => $uploadResult['external_id'],
        ]);

        return $media;
    }

    /**
     * Delete media file.
     */
    public function deleteMedia(Media $media): bool
    {
        if ($media->isStoredExternally()) {
            // Get client_id through appointment or fallback to user's default client
            $clientId = $media->encounter?->appointment?->client_id ?? auth()->user()?->default_client_id;
            $provider = StorageProviderFactory::make($clientId);

            if ($provider && $media->file_path) {
                // Usar file_path para eliminar, no external_id
                $provider->delete($media->file_path);

                Log::info('Media deleted from external storage', [
                    'media_id' => $media->id,
                    'path' => $media->file_path,
                ]);
            }
        } else {
            if (Storage::disk($media->storage_disk)->exists($media->file_path)) {
                Storage::disk($media->storage_disk)->delete($media->file_path);
            }
        }

        return $media->delete();
    }

    /**
     * Get accessible URL for media.
     */
    public function getMediaUrl(Media $media, int $expirationMinutes = 60): string
    {
        if ($media->isStoredExternally()) {
            // Get client_id through appointment or fallback to user's default client
            $clientId = $media->encounter?->appointment?->client_id ?? auth()->user()?->default_client_id;
            $provider = StorageProviderFactory::make($clientId);

            if ($provider && $media->external_id) {
                try {
                    return $provider->getTemporaryUrl($media->external_id, $expirationMinutes);
                } catch (\Exception $e) {
                    Log::error('Error getting external media URL', [
                        'media_id' => $media->id,
                        'error' => $e->getMessage(),
                    ]);

                    // Si falla obtener URL de Dropbox, intentar con file_path
                    if ($media->file_path) {
                        try {
                            return $provider->getTemporaryUrl($media->file_path, $expirationMinutes);
                        } catch (\Exception $e2) {
                            Log::error('Error getting external media URL with path', [
                                'media_id' => $media->id,
                                'error' => $e2->getMessage(),
                            ]);
                        }
                    }

                    // URL de placeholder si todo falla
                    return '#';
                }
            }

            // Si no hay provider pero está marcado como externo, devolver placeholder
            return '#';
        }

        // Para archivos locales
        return Storage::disk($media->storage_disk)->url($media->file_path);
    }

    /**
     * Generate external storage path.
     */
    protected function generateExternalPath(Encounter $encounter, UploadedFile $file): string
    {
        $timestamp = now()->format('YmdHis');
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $sanitizedFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

        return sprintf(
            '/consultations/%s/%s/%s_%s.%s',
            $encounter->patient_id,
            $encounter->id,
            $timestamp,
            $sanitizedFilename,
            $extension
        );
    }

    /**
     * Guess file type from MIME type.
     */
    protected function guessType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();

        return match (true) {
            str_starts_with($mimeType, 'image/') => 'photo',
            str_starts_with($mimeType, 'video/') => 'video',
            str_starts_with($mimeType, 'audio/') => 'audio',
            default => 'document',
        };
    }
}
