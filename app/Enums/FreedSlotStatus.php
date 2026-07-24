<?php

namespace App\Enums;

enum FreedSlotStatus: string
{
    case Available = 'available';
    case Matched = 'matched';
    case Expired = 'expired';
    case ManuallyFilled = 'manually_filled';

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Matched => 'Emparejado',
            self::Expired => 'Expirado',
            self::ManuallyFilled => 'Llenado Manualmente',
        };
    }

    /**
     * Get badge CSS class
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'badge bg-success',
            self::Matched => 'badge bg-info',
            self::Expired => 'badge bg-secondary',
            self::ManuallyFilled => 'badge bg-primary',
        };
    }
}
