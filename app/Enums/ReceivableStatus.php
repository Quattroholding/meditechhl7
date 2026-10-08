<?php

namespace App\Enums;

enum ReceivableStatus: string
{
    case PENDING = 'pending';       // Pendiente
    case PARTIAL = 'partial';       // Parcial
    case PAID = 'paid';             // Pagada
    case OVERDUE = 'overdue';       // Vencida
    case CANCELLED = 'cancelled';   // Anulada

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PARTIAL => 'Parcial',
            self::PAID => 'Pagada',
            self::OVERDUE => 'Vencida',
            self::CANCELLED => 'Anulada',
        };
    }
}
