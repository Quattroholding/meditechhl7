<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HistoryDownload extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'requested_by_user_id',
        'status',
        'file_path',
        'total_encounters',
        'processed_encounters',
        'completed_at',
        'expires_at',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    // Methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markAsCompleted(string $filePath): void
    {
        $this->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'completed_at' => now(),
            'expires_at' => now()->addHours(24), // 24 horas para descargar
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public function updateProgress(int $processed): void
    {
        $this->update([
            'processed_encounters' => $processed,
        ]);
    }

    public function deleteFile(): void
    {
        if ($this->file_path) {
            $fullPath = storage_path('app/'.$this->file_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    public function getDownloadUrl(): string
    {
        return route('patient.history.download', ['id' => $this->id]);
    }

    public function getProgressPercentage(): int
    {
        if ($this->total_encounters === 0) {
            return 0;
        }

        return (int) round(($this->processed_encounters / $this->total_encounters) * 100);
    }
}
