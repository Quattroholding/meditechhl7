<?php

namespace App\Enums;

enum WaitlistStatus: string
{
    case Active = 'active';
    case Assigned = 'assigned';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activa',
            self::Assigned => 'Asignada',
            self::Expired => 'Expirada',
            self::Cancelled => 'Cancelada',
        };
    }

    /**
     * Get badge CSS class
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'badge bg-info',
            self::Assigned => 'badge bg-success',
            self::Expired => 'badge bg-secondary',
            self::Cancelled => 'badge bg-danger',
        };
    }

    /**
     * Get all options as array [value => label]
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
