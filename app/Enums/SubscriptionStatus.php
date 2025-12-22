<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case PENDING_ACTIVATION = 'pending_activation';
    case TRIAL = 'trial';
    case ACTIVE = 'active';
    case PAST_DUE = 'past_due';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_ACTIVATION => 'Pendiente de Activación',
            self::TRIAL => 'Período de Prueba',
            self::ACTIVE => 'Activa',
            self::PAST_DUE => 'Pago Vencido',
            self::SUSPENDED => 'Suspendida',
            self::CANCELLED => 'Cancelada',
            self::EXPIRED => 'Expirada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING_ACTIVATION => 'warning',
            self::TRIAL => 'info',
            self::ACTIVE => 'success',
            self::PAST_DUE => 'warning',
            self::SUSPENDED => 'danger',
            self::CANCELLED => 'secondary',
            self::EXPIRED => 'dark',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::TRIAL, self::ACTIVE]);
    }
}
