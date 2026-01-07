<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupReminderConfig extends Model
{
    protected $fillable = [
        'reminder_key',
        'title',
        'is_active',
        'is_required',
        'order',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get active reminders ordered
     */
    public static function getActiveReminders(): array
    {
        return static::where('is_active', true)
            ->orderBy('order')
            ->pluck('reminder_key')
            ->toArray();
    }

    /**
     * Check if a reminder is active
     */
    public static function isReminderActive(string $key): bool
    {
        return static::where('reminder_key', $key)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if a reminder is required
     */
    public static function isReminderRequired(string $key): bool
    {
        $config = static::where('reminder_key', $key)->first();

        return $config ? $config->is_required : true;
    }
}
