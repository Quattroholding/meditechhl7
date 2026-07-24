<?php

namespace App\Enums;

enum FreedSlotSource: string
{
    case Cancellation = 'cancellation';
    case Noshow = 'noshow';
    case Manual = 'manual';

    /**
     * Get the label for the source
     */
    public function label(): string
    {
        return match ($this) {
            self::Cancellation => 'Cancelación',
            self::Noshow => 'No Asistió',
            self::Manual => 'Manual',
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
