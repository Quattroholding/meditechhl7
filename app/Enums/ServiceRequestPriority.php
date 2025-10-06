<?php

namespace App\Enums;

enum ServiceRequestPriority: string
{
    case ROUTINE = 'routine';
    case URGENT = 'urgent';
    case ASAP = 'asap';
    case STAT = 'stat';

    public function label(): string
    {
        return match ($this) {
            self::ROUTINE => 'Rutina',
            self::URGENT => 'Urgente',
            self::ASAP => 'Lo Antes Posible',
            self::STAT => 'Inmediato',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ROUTINE => 'bg-info',
            self::URGENT => 'bg-warning',
            self::ASAP => 'bg-orange',
            self::STAT => 'bg-danger',
        };
    }

    public static function fromString(string $priority): ?self
    {
        return match ($priority) {
            'routine' => self::ROUTINE,
            'urgent' => self::URGENT,
            'asap' => self::ASAP,
            'stat' => self::STAT,
            default => null,
        };
    }
}
