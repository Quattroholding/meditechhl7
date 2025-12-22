<?php

namespace App\Enums;

enum ReferralStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REWARDED = 'rewarded';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::REWARDED => 'Rewarded',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::REWARDED => 'success',
            self::EXPIRED => 'dark',
            self::CANCELLED => 'danger',
        };
    }
}
