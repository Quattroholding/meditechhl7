<?php

namespace App\Enums;

enum WaitlistUrgencyLevel: string
{
    case Routine = 'routine';
    case Urgent = 'urgent';
    case VeryUrgent = 'very_urgent';
    case Emergency = 'emergency';

    /**
     * Get the label for the urgency level
     */
    public function label(): string
    {
        return match ($this) {
            self::Routine => 'Rutinaria',
            self::Urgent => 'Urgente',
            self::VeryUrgent => 'Muy Urgente',
            self::Emergency => 'Emergencia',
        };
    }

    /**
     * Get the priority points (0-40)
     */
    public function priorityPoints(): int
    {
        return match ($this) {
            self::Routine => 0,
            self::Urgent => 20,
            self::VeryUrgent => 30,
            self::Emergency => 40,
        };
    }

    /**
     * Get badge CSS class
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Routine => 'badge bg-secondary',
            self::Urgent => 'badge bg-warning text-dark',
            self::VeryUrgent => 'badge bg-danger',
            self::Emergency => 'badge bg-dark',
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
