<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Scopes\DocumentUploadScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class DocumentUpload extends BaseModel
{
    protected $fillable = [
        'client_id',
        'branch_id',
        'document_type',
        'status',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_by_user_id',
        'processing_started_at',
        'processing_completed_at',
        'invoice_number',
        'invoice_date',
        'subtotal',
        'total_tax',
        'total',
    ];

    protected $casts = [
        'document_type' => DocumentType::class,
        'status' => DocumentStatus::class,
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new DocumentUploadScope);

        // Generate UUID before creating
        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    // ===== Relationships =====

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function parseResult(): HasOne
    {
        return $this->hasOne(DocumentParseResult::class);
    }

    public function approval(): HasOne
    {
        return $this->hasOne(DocumentApproval::class);
    }

    // ===== Methods =====

    public function markAsParsing(): void
    {
        $this->update([
            'status' => DocumentStatus::PARSING,
            'processing_started_at' => now(),
        ]);
    }

    public function markAsParsed(): void
    {
        $this->update([
            'status' => DocumentStatus::PARSED,
            'processing_completed_at' => now(),
        ]);
    }

    public function markAsParsingFailed(?string $error = null): void
    {
        $this->update([
            'status' => DocumentStatus::PARSING_FAILED,
            'processing_completed_at' => now(),
        ]);

        if ($error) {
            StatusHistoryLog::create([
                'model_name' => self::class,
                'table_name' => $this->getTable(),
                'record_id' => $this->id,
                'old_status' => DocumentStatus::PARSING->value,
                'new_status' => DocumentStatus::PARSING_FAILED->value,
                'observation' => $error,
                'change_type' => 'auto',
                'user_id' => auth()->id() ?? 1,
            ]);
        }
    }

    public function canBeApproved(): bool
    {
        return $this->status === DocumentStatus::PARSED && $this->parseResult()->exists();
    }

    public function markAsApproved(): void
    {
        $this->update(['status' => DocumentStatus::APPROVED]);

        StatusHistoryLog::create([
            'model_name' => self::class,
            'table_name' => $this->getTable(),
            'record_id' => $this->id,
            'old_status' => DocumentStatus::PARSED->value,
            'new_status' => DocumentStatus::APPROVED->value,
            'observation' => 'Document approved by user',
            'change_type' => 'manual',
            'user_id' => auth()->id() ?? 1,
        ]);
    }

    public function markAsRejected(?string $reason = null): void
    {
        $this->update(['status' => DocumentStatus::REJECTED]);

        StatusHistoryLog::create([
            'model_name' => self::class,
            'table_name' => $this->getTable(),
            'record_id' => $this->id,
            'old_status' => DocumentStatus::PARSED->value,
            'new_status' => DocumentStatus::REJECTED->value,
            'observation' => $reason ?? 'Document rejected by user',
            'change_type' => 'manual',
            'user_id' => auth()->id() ?? 1,
        ]);
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => DocumentStatus::PROCESSING,
            'processing_started_at' => now(),
        ]);
    }

    public function markAsProcessed(): void
    {
        $this->update([
            'status' => DocumentStatus::PROCESSED,
            'processing_completed_at' => now(),
        ]);

        StatusHistoryLog::create([
            'model_name' => self::class,
            'table_name' => $this->getTable(),
            'record_id' => $this->id,
            'old_status' => DocumentStatus::APPROVED->value,
            'new_status' => DocumentStatus::PROCESSED->value,
            'observation' => 'Document processed successfully',
            'change_type' => 'auto',
            'user_id' => auth()->id() ?? 1,
        ]);
    }

    public function markAsProcessingFailed(?string $error = null): void
    {
        $this->update([
            'status' => DocumentStatus::PROCESSING_FAILED,
            'processing_completed_at' => now(),
        ]);

        if ($error) {
            StatusHistoryLog::create([
                'model_name' => self::class,
                'table_name' => $this->getTable(),
                'record_id' => $this->id,
                'old_status' => DocumentStatus::PROCESSING->value,
                'new_status' => DocumentStatus::PROCESSING_FAILED->value,
                'observation' => $error,
                'change_type' => 'auto',
                'user_id' => auth()->id() ?? 1,
            ]);
        }
    }
}
