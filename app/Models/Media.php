<?php

namespace App\Models;

use App\Services\MediaStorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'fhir_id',
        'patient_id',
        'encounter_id',
        'practitioner_id',
        'subject_type',
        'subject_id',
        'identifier',
        'status',
        'type',
        'modality',
        'view',
        'content_type',
        'file_path',
        'file_name',
        'file_size',
        'width',
        'height',
        'frames',
        'duration',
        'note',
        'created_datetime',
        'issued_datetime',
        'device_name',
        'extension',
        'storage_disk',
        'external_id',
        'external_metadata',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'frames' => 'integer',
            'duration' => 'decimal:2',
            'created_datetime' => 'datetime',
            'issued_datetime' => 'datetime',
            'device_name' => 'array',
            'extension' => 'array',
            'external_metadata' => 'array',
        ];
    }

    // Relationships

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    // Scopes

    public function scopePhotos($query)
    {
        return $query->where('type', 'photo');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeByModality($query, string $modality)
    {
        return $query->where('modality', $modality);
    }

    public function scopeDermoscopy($query)
    {
        return $query->where('modality', 'dermoscopy');
    }

    public function scopeForSubject($query, string $type, int $id)
    {
        return $query->where('subject_type', $type)
            ->where('subject_id', $id);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_datetime', '>=', now()->subDays($days));
    }

    // Helper methods

    public function isStoredExternally(): bool
    {
        return $this->storage_disk !== 'public' && ! empty($this->external_id);
    }

    public function getAccessibleUrlAttribute(): string
    {
        if ($this->isStoredExternally()) {
            $service = app(MediaStorageService::class);

            return $service->getMediaUrl($this, 60);
        }

        return $this->url;
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->storage_disk)->url($this->file_path);
    }

    public function getFullUrlAttribute(): string
    {
        if ($this->isStoredExternally()) {
            return $this->getAccessibleUrlAttribute();
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->type !== 'photo') {
            return null;
        }

        $thumbnailPath = str_replace($this->file_name, 'thumb_'.$this->file_name, $this->file_path);

        if (Storage::exists($thumbnailPath)) {
            return Storage::url($thumbnailPath);
        }

        return $this->url;
    }

    public function getFileSizeHumanAttribute(): string
    {
        if (! $this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2).' '.$units[$unitIndex];
    }

    public function getDimensionsAttribute(): ?string
    {
        if ($this->width && $this->height) {
            return "{$this->width}x{$this->height}";
        }

        return null;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->content_type, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->content_type, 'video/');
    }

    public function isAudio(): bool
    {
        return str_starts_with($this->content_type, 'audio/');
    }

    public function getIconAttribute(): string
    {
        return match (true) {
            $this->isImage() => 'fa-image',
            $this->isVideo() => 'fa-video',
            $this->isAudio() => 'fa-volume-up',
            default => 'fa-file',
        };
    }

    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'photo' => 'Fotografía',
            'video' => 'Video',
            'audio' => 'Audio',
            default => ucfirst($this->type),
        };
    }

    public function getModalityDisplayAttribute(): string
    {
        return match ($this->modality) {
            'dermoscopy' => 'Dermatoscopia',
            'photograph' => 'Fotografía',
            'clinical' => 'Clínica',
            default => ucfirst($this->modality ?? 'N/A'),
        };
    }

    public function toFHIR(): array
    {
        return [
            'resourceType' => 'Media',
            'id' => $this->fhir_id,
            'identifier' => [
                [
                    'system' => 'urn:meditech:media',
                    'value' => $this->identifier,
                ],
            ],
            'status' => $this->status,
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/media-type',
                        'code' => $this->type,
                    ],
                ],
            ],
            'modality' => $this->modality ? [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/media-modality',
                        'code' => $this->modality,
                    ],
                ],
            ] : null,
            'subject' => [
                'reference' => "Patient/{$this->patient->fhir_id}",
            ],
            'encounter' => $this->encounter ? [
                'reference' => "Encounter/{$this->encounter->fhir_id}",
            ] : null,
            'created' => $this->created_datetime->toIso8601String(),
            'issued' => $this->issued_datetime?->toIso8601String(),
            'operator' => $this->practitioner ? [
                'reference' => "Practitioner/{$this->practitioner->fhir_id}",
            ] : null,
            'device' => $this->device_name,
            'height' => $this->height,
            'width' => $this->width,
            'frames' => $this->frames,
            'duration' => $this->duration,
            'content' => [
                'contentType' => $this->content_type,
                'url' => $this->full_url,
                'size' => $this->file_size,
            ],
            'note' => $this->note ? [
                [
                    'text' => $this->note,
                ],
            ] : null,
        ];
    }

    public function delete()
    {
        // Para archivos externos, MediaStorageService maneja la eliminación del archivo
        // Este método solo elimina archivos locales del disco antes de borrar el registro
        if (!$this->isStoredExternally()) {
            // Delete physical file when deleting media record
            if (Storage::disk($this->storage_disk)->exists($this->file_path)) {
                Storage::disk($this->storage_disk)->delete($this->file_path);

                // Also delete thumbnail if exists
                $thumbnailPath = str_replace($this->file_name, 'thumb_'.$this->file_name, $this->file_path);
                if (Storage::disk($this->storage_disk)->exists($thumbnailPath)) {
                    Storage::disk($this->storage_disk)->delete($thumbnailPath);
                }
            }
        }

        // Siempre eliminar el registro de la base de datos
        return parent::delete();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'media-'.uniqid();
            }

            if (empty($model->created_datetime)) {
                $model->created_datetime = now();
            }

            if (empty($model->status)) {
                $model->status = 'completed';
            }
        });
    }
}
