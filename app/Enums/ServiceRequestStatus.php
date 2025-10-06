<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case ON_HOLD = 'on-hold';
    case REVOKED = 'revoked';
    case COMPLETED = 'completed';
    case ENTERED_IN_ERROR = 'entered-in-error';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::ACTIVE => 'Activo',
            self::ON_HOLD => 'En Espera',
            self::REVOKED => 'Revocado',
            self::COMPLETED => 'Completado',
            self::ENTERED_IN_ERROR => 'Error de Registro',
            self::UNKNOWN => 'Desconocido',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-secondary',
            self::ACTIVE => 'bg-primary',
            self::ON_HOLD => 'bg-warning',
            self::REVOKED => 'bg-danger',
            self::COMPLETED => 'bg-success',
            self::ENTERED_IN_ERROR => 'bg-dark',
            self::UNKNOWN => 'bg-secondary',
        };
    }

    public static function fromString(string $status): ?self
    {
        return match ($status) {
            'draft' => self::DRAFT,
            'active' => self::ACTIVE,
            'on-hold' => self::ON_HOLD,
            'revoked' => self::REVOKED,
            'completed' => self::COMPLETED,
            'entered-in-error' => self::ENTERED_IN_ERROR,
            'unknown' => self::UNKNOWN,
            default => null,
        };
    }
}
