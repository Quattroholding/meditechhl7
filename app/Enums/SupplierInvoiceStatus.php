<?php

namespace App\Enums;

enum SupplierInvoiceStatus: string
{
    case DRAFT = 'draft';
    case REGISTERED = 'registered';
    case APPROVED = 'approved';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::REGISTERED => 'Registrada',
            self::APPROVED => 'Aprobada',
            self::PARTIAL => 'Pago Parcial',
            self::PAID => 'Pagada',
            self::OVERDUE => 'Vencida',
            self::CANCELLED => 'Cancelada',
        };
    }

    public static function getLabel(string $value): string
    {
        return self::tryFrom($value)?->label() ?? $value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-secondary',
            self::REGISTERED => 'bg-secondary',
            self::APPROVED => 'bg-info',
            self::PARTIAL => 'bg-warning',
            self::PAID => 'bg-success',
            self::OVERDUE => 'bg-danger',
            self::CANCELLED => 'bg-dark',
        };
    }

    public static function getBadgeClass(string $value): string
    {
        return self::tryFrom($value)?->badgeClass() ?? 'bg-secondary';
    }
}
