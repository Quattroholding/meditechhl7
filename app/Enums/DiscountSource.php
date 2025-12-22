<?php

namespace App\Enums;

enum DiscountSource: string
{
    case REFERRAL = 'referral';
    case PROMOTION = 'promotion';
    case COURTESY = 'courtesy';
    case LOYALTY = 'loyalty';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::REFERRAL => 'Referral',
            self::PROMOTION => 'Promotion',
            self::COURTESY => 'Courtesy',
            self::LOYALTY => 'Loyalty',
            self::OTHER => 'Other',
        };
    }
}
