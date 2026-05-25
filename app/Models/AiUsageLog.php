<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'service',
        'operation',
        'model',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'estimated_cost_cents',
        'request_summary',
        'audio_duration_seconds',
        'audio_size_bytes',
        'encounter_id',
        'patient_id',
        'metadata',
        'status',
        'error_message',
        'response_time_ms',
        'api_request_id',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'total_tokens' => 'integer',
            'estimated_cost_cents' => 'integer',
            'audio_duration_seconds' => 'integer',
            'audio_size_bytes' => 'integer',
            'response_time_ms' => 'integer',
        ];
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Scopes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeForService($query, $service)
    {
        return $query->where('service', $service);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Accessors
     */
    public function getEstimatedCostAttribute(): float
    {
        return $this->estimated_cost_cents / 100; // Convert cents to dollars
    }

    public function getFormattedCostAttribute(): string
    {
        return '$'.number_format($this->estimated_cost / 100, 4);
    }

    /**
     * Helper Methods
     */
    public static function logUsage(array $data): self
    {
        // Auto-calculate total tokens if not provided
        if (! isset($data['total_tokens'])) {
            $data['total_tokens'] = ($data['input_tokens'] ?? 0) + ($data['output_tokens'] ?? 0);
        }

        return self::create($data);
    }

    /**
     * Get total cost for a user in a date range
     */
    public static function getTotalCostForUser(int $userId, $startDate = null, $endDate = null): float
    {
        $query = self::forUser($userId)->successful();

        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }

        return $query->sum('estimated_cost_cents') / 100;
    }

    /**
     * Get total tokens for a user in a date range
     */
    public static function getTotalTokensForUser(int $userId, $startDate = null, $endDate = null): int
    {
        $query = self::forUser($userId)->successful();

        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }

        return $query->sum('total_tokens');
    }

    /**
     * Get usage statistics grouped by service
     */
    public static function getUsageByService(int $userId, $startDate = null, $endDate = null): array
    {
        $query = self::forUser($userId)->successful();

        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }

        return $query->selectRaw('service, COUNT(*) as count, SUM(total_tokens) as total_tokens, SUM(estimated_cost_cents) as total_cost_cents')
            ->groupBy('service')
            ->get()
            ->map(function ($item) {
                return [
                    'service' => $item->service,
                    'count' => $item->count,
                    'total_tokens' => $item->total_tokens,
                    'total_cost' => $item->total_cost_cents / 100,
                ];
            })
            ->toArray();
    }
}
