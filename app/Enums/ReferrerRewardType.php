<?php

namespace App\Enums;

enum ReferrerRewardType: string
{
    case PERCENTAGE_DISCOUNT = 'percentage_discount';
    case FIXED_DISCOUNT = 'fixed_discount';
    case FREE_MONTHS = 'free_months';
    case CREDIT = 'credit';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE_DISCOUNT => 'Percentage Discount',
            self::FIXED_DISCOUNT => 'Fixed Discount',
            self::FREE_MONTHS => 'Free Months',
            self::CREDIT => 'Credit',
        };
    }

    public function format(float $value): string
    {
        return match ($this) {
            self::PERCENTAGE_DISCOUNT => $value.'%',
            self::FIXED_DISCOUNT => '$'.number_format($value, 2),
            self::FREE_MONTHS => $value.' '.($value === 1.0 ? 'month' : 'months'),
            self::CREDIT => '$'.number_format($value, 2).' credit',
        };
    }
}
