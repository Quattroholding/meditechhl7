<?php

namespace App\Enums;

enum InvoiceItemType: string
{
    case SUBSCRIPTION_BASE = 'subscription_base';
    case EXTRA_DOCTORS = 'extra_doctors';
    case ADJUSTMENT = 'adjustment';
    case CREDIT = 'credit';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SUBSCRIPTION_BASE => 'Subscription Base',
            self::EXTRA_DOCTORS => 'Extra Doctors',
            self::ADJUSTMENT => 'Adjustment',
            self::CREDIT => 'Credit',
            self::OTHER => 'Other',
        };
    }
}
