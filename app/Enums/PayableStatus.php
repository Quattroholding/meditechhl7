<?php

namespace App\Enums;

enum PayableStatus: string
{
    case DRAFT = 'draft';               // Borrador
    case REGISTERED = 'registered';     // Registrada
    case APPROVED = 'approved';         // Aprobada
    case PARTIAL = 'partial';           // Parcialmente Pagada
    case PAID = 'paid';                 // Pagada
    case OVERDUE = 'overdue';           // Vencida
    case CANCELLED = 'cancelled';       // Anulada

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::REGISTERED => 'Registrada',
            self::APPROVED => 'Aprobada',
            self::PARTIAL => 'Parcialmente Pagada',
            self::PAID => 'Pagada',
            self::OVERDUE => 'Vencida',
            self::CANCELLED => 'Anulada',
        };
    }
}
