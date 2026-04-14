<?php

namespace App\Enums;

enum NeoPaymentStatus: string
{
    case PENDING = 'PENDING';
    case AUTHORIZED = 'AUTHORIZED';
    case DENIED = 'DENIED';
    case REFUSED = 'REFUSED';
    case FAILED = 'FAILED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::AUTHORIZED => 'Autorizado',
            self::DENIED => 'Denegado',
            self::REFUSED => 'Rechazado',
            self::FAILED => 'Fallido',
        };
    }

    public function isSuccess(): bool
    {
        return $this === self::AUTHORIZED;
    }

    public function isFailed(): bool
    {
        return in_array($this, [self::DENIED, self::REFUSED, self::FAILED]);
    }
}
