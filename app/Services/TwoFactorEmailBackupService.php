<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\TwoFactorBackupCodeNotification;
use Illuminate\Support\Facades\Cache;

class TwoFactorEmailBackupService
{
    /**
     * Generate and send a backup code via email.
     */
    public function sendEmailBackupCode(User $user): string
    {
        $code = $this->generateEmailBackupCode($user);

        $user->notify(new TwoFactorBackupCodeNotification($code));

        return $code;
    }

    /**
     * Generate a backup code and store it in cache.
     */
    public function generateEmailBackupCode(User $user): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $key = $this->getCacheKey($user->id);
        $expiry = config('two-factor.email_backup.expiry', 15);

        Cache::put($key, $code, now()->addMinutes($expiry));

        return $code;
    }

    /**
     * Verify an email backup code.
     */
    public function verifyEmailBackupCode(User $user, string $code): bool
    {
        $key = $this->getCacheKey($user->id);
        $storedCode = Cache::get($key);

        if (! $storedCode) {
            return false;
        }

        if ($storedCode === $code) {
            // Code is valid, remove it from cache (one-time use)
            Cache::forget($key);

            return true;
        }

        return false;
    }

    /**
     * Check if a user has a pending email backup code.
     */
    public function hasPendingCode(User $user): bool
    {
        return Cache::has($this->getCacheKey($user->id));
    }

    /**
     * Get the cache key for a user's email backup code.
     */
    protected function getCacheKey(int $userId): string
    {
        return "2fa_email_backup_{$userId}";
    }

    /**
     * Clean up expired codes (called by scheduled task).
     */
    public function cleanupExpiredCodes(): void
    {
        // Cache TTL handles this automatically
        // This method exists for explicit cleanup if needed
    }
}
