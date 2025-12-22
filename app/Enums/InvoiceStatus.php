<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIALLY_PAID = 'partially_paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::PENDING => 'Pendiente',
            self::PAID => 'Pagada',
            self::PARTIALLY_PAID => 'Parcialmente Pagada',
            self::OVERDUE => 'Vencida',
            self::CANCELLED => 'Cancelada',
            self::REFUNDED => 'Reembolsada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::PARTIALLY_PAID => 'info',
            self::OVERDUE => 'danger',
            self::CANCELLED => 'dark',
            self::REFUNDED => 'warning',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isPayable(): bool
    {
        return in_array($this, [self::PENDING, self::PARTIALLY_PAID, self::OVERDUE]);
    }
}
