<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppConversationUsage extends Model
{
    protected $table = 'whatsapp_conversation_usage';

    protected $fillable = [
        'phone_number',
        'conversation_id',
        'message_id',
        'year',
        'month',
        'type',
        'conversation_started_at',
    ];

    protected $casts = [
        'conversation_started_at' => 'datetime',
    ];

    /**
     * Get business-initiated conversations count for current month
     */
    public static function getCurrentMonthBusinessCount(): int
    {
        return static::where('type', 'business_initiated')
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->count();
    }

    /**
     * Check if business-initiated conversations are available (< 1000 this month)
     */
    public static function hasAvailableQuota(): bool
    {
        return static::getCurrentMonthBusinessCount() < 1000;
    }

    /**
     * Get remaining quota for this month
     */
    public static function getRemainingQuota(): int
    {
        return max(0, 1000 - static::getCurrentMonthBusinessCount());
    }
}
