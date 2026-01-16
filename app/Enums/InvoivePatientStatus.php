<?php

namespace App\Enums;

enum InvoivePatientStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case PARTIAL = 'partially_paid';
    case OVERDUE = 'overdue';

    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'No Pagada',
            self::PAID => 'Pagada',
            self::PARTIAL => 'Pago Parcial',
            self::OVERDUE => 'Vencida',
            self::PENDING => 'Pendiente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'custom-badge status-pink',
            self::PAID => 'custom-badge status-green',
            self::PARTIAL => 'custom-badge status-orange',
            self::OVERDUE => 'custom-badge status-default',
            self::PENDING => 'custom-badge Pendiente',
        };
    }
}
