<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    case REJECTED = 'rejected';
    case REFUNDED = 'refunded';

    case CANCELLED = 'cancelled';

    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::COMPLETED => 'Completado',
            self::FAILED => 'Fallido',
            self::REJECTED => 'Rechazado',
            self::REFUNDED => 'Reembolsado',
            self::CANCELLED => 'Cancelado',
            self::EXPIRED => 'Expirado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::REJECTED => 'danger',
            self::CANCELLED => 'danger',
            self::EXPIRED => 'danger',
            self::REFUNDED => 'info',
        };
    }

    public function isSuccessful(): bool
    {
        return $this === self::COMPLETED;
    }
}
