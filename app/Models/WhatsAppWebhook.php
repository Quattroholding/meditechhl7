<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppWebhook extends Model
{
    protected $table='whatsapp_webhooks';
    protected $fillable = [
        'event_type',
        'message_id',
        'phone_number_id',
        'from',
        'to',
        'status',
        'error_message',
        'raw_payload',
        'metadata',
        'webhook_received_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'metadata' => 'array',
        'webhook_received_at' => 'datetime',
    ];

    /**
     * Scope to filter by event type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope to filter by message ID
     */
    public function scopeForMessage($query, string $messageId)
    {
        return $query->where('message_id', $messageId);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if webhook contains an error
     */
    public function hasError(): bool
    {
        return ! empty($this->error_message) || $this->status === 'failed';
    }

    /**
     * Get formatted error details
     */
    public function getErrorDetails(): ?string
    {
        if (! $this->hasError()) {
            return null;
        }

        return $this->error_message ?? 'Status: '.$this->status;
    }
}
