<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'performed_by',
        'action',
        'ip_address',
        'user_agent',
        'reason',
    ];

    /**
     * Get the user this log entry belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who performed this action.
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Create a log entry for a 2FA action.
     */
    public static function log(int $userId, string $action, ?int $performedBy = null, ?string $reason = null): void
    {
        self::create([
            'user_id' => $userId,
            'performed_by' => $performedBy ?? $userId,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'reason' => $reason,
        ]);
    }
}
