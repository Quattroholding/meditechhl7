<?php

namespace App\Enums;

enum ChargeItemStatus: string
{
    case PLANNED = 'planned';
    case BILLABLE = 'billable';
    case NOT_BILLABLE = 'not-billable';
    case ABORTED = 'aborted';
    case BILLED = 'billed';
    case ENTERED_IN_ERROR = 'entered-in-error';
    case UNKNOWN = 'unknown';

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::PLANNED => 'Planificado',
            self::BILLABLE => 'Facturable',
            self::NOT_BILLABLE => 'No Facturable',
            self::ABORTED => 'Abortado',
            self::BILLED => 'Facturado',
            self::ENTERED_IN_ERROR => 'Ingresado con Error',
            self::UNKNOWN => 'Desconocido',
        };
    }

    /**
     * Get the badge color for the status
     */
    public function color(): string
    {
        return match ($this) {
            self::PLANNED => 'info',
            self::BILLABLE => 'success',
            self::NOT_BILLABLE => 'secondary',
            self::ABORTED => 'danger',
            self::BILLED => 'primary',
            self::ENTERED_IN_ERROR => 'warning',
            self::UNKNOWN => 'dark',
        };
    }

    /**
     * Get all statuses as array
     */
    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Get all statuses with labels
     */
    public static function options(): array
    {
        return array_map(
            fn ($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
