<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentParseResult extends BaseModel
{
    protected $fillable = [
        'document_upload_id',
        'raw_response',
        'extracted_data',
        'confidence_score',
        'detected_format',
        'has_warnings',
        'has_errors',
        'validation_messages',
        'batch_info',
        'additional_fields',
        'manually_edited',
        'edited_by_user_id',
        'edited_at',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'extracted_data' => 'array',
        'validation_messages' => 'array',
        'batch_info' => 'array',
        'additional_fields' => 'array',
        'has_warnings' => 'boolean',
        'has_errors' => 'boolean',
        'manually_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    // ===== Relationships =====

    public function documentUpload(): BelongsTo
    {
        return $this->belongsTo(DocumentUpload::class);
    }

    public function editedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by_user_id');
    }

    // ===== Methods =====

    public function hasErrors(): bool
    {
        return $this->has_errors;
    }

    public function hasWarnings(): bool
    {
        return $this->has_warnings;
    }

    public function getValidationMessages(): array
    {
        return $this->validation_messages ?? [];
    }

    public function getExtractedItems(): array
    {
        return $this->extracted_data['items'] ?? [];
    }
}
