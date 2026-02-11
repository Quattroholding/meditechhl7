<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case Proposed = 'proposed';
    case Pending = 'pending';
    case Booked = 'booked';
    case Confirm = 'confirm';
    case Arrived = 'arrived';
    case Fulfilled = 'fulfilled';
    case Cancelled = 'cancelled';
    case NoShow = 'noshow';
    case EnteredInError = 'entered-in-error';
    case CheckedIn = 'checked-in';
    case Waitlist = 'waitlist';

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::Proposed => 'Propuesta',
            self::Pending => 'Pendiente',
            self::Booked => 'Reservado',
            self::Confirm => 'Confirmado',
            self::Arrived => 'Llegó',
            self::Fulfilled => 'Cumplido',
            self::Cancelled => 'Cancelado',
            self::NoShow => 'No asistió',
            self::EnteredInError => 'Error',
            self::CheckedIn => 'En Progreso',
            self::Waitlist => 'Lista de espera',
        };
    }

    /**
     * Get the hex color for the status
     */
    public function color(): string
    {
        return match ($this) {
            self::Proposed => 'dedede',
            self::Pending => 'FFA500',
            self::Booked => '4CAF50',
            self::Confirm => '55ce63',
            self::Arrived => '00BCD4',
            self::Fulfilled => '2196F3',
            self::Cancelled => 'F44336',
            self::NoShow => '9E9E9E',
            self::EnteredInError => 'FF5252',
            self::CheckedIn => '7C4DFF',
            self::Waitlist => 'FF9800',
        };
    }

    /**
     * Get the badge CSS class for the status
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Proposed => 'badge appointment-status-proposed',
            self::Pending => 'badge appointment-status-pending',
            self::Booked => 'badge appointment-status-booked',
            self::Confirm => 'badge appointment-status-confirm',
            self::Arrived => 'badge appointment-status-arrived',
            self::Fulfilled => 'badge appointment-status-fulfilled',
            self::Cancelled => 'badge appointment-status-cancelled',
            self::NoShow => 'badge appointment-status-noshow',
            self::EnteredInError => 'bg-danger',
            self::CheckedIn => 'badge appointment-status-checked-in',
            self::Waitlist => 'bg-warning',
        };
    }

    /**
     * Get status enum from string value
     */
    public static function fromString(?string $status): ?self
    {
        if ($status === null) {
            return null;
        }

        return self::tryFrom($status);
    }

    /**
     * Get label from string value
     */
    public static function getLabelFromString(?string $status): string
    {
        $enum = self::fromString($status);

        return $enum?->label() ?? $status ?? 'N/A';
    }

    /**
     * Get badge class from string value
     */
    public static function getBadgeClassFromString(?string $status): string
    {
        $enum = self::fromString($status);

        return $enum?->badgeClass() ?? 'bg-secondary';
    }

    /**
     * Get color from string value
     */
    public static function getColorFromString(?string $status): string
    {
        $enum = self::fromString($status);

        return $enum?->color() ?? '9E9E9E';
    }

    /**
     * Get all status as array [value => label]
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
