<?php

namespace App\Enums;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED_AMOUNT = 'fixed_amount';
    case FREE_MONTHS = 'free_months';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Percentage',
            self::FIXED_AMOUNT => 'Fixed Amount',
            self::FREE_MONTHS => 'Free Months',
        };
    }

    public function format(float $value): string
    {
        return match ($this) {
            self::PERCENTAGE => $value.'%',
            self::FIXED_AMOUNT => '$'.number_format($value, 2),
            self::FREE_MONTHS => $value.' '.($value === 1.0 ? 'month' : 'months'),
        };
    }
}
