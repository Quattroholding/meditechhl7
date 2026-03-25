<?php

namespace App\Enums;

enum BillingPeriod: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    case DAILY = 'daily';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly',
            self::QUARTERLY => 'Quarterly',
            self::YEARLY => 'Yearly',
            self::DAILY => 'Diario',
        };
    }

    public function days(): int
    {
        return match ($this) {
            self::MONTHLY => 30,
            self::QUARTERLY => 90,
            self::YEARLY => 365,
            self::DAILY => 1,
        };
    }

    public function months(): int
    {
        return match ($this) {
            self::MONTHLY => 1,
            self::QUARTERLY => 3,
            self::YEARLY => 12,
            self::DAILY => 0,
        };
    }
}
