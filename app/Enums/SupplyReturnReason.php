<?php

namespace App\Enums;

enum SupplyReturnReason: string
{
    case WRONG_ITEM = 'wrong_item';
    case WRONG_QUANTITY = 'wrong_quantity';
    case NOT_USED = 'not_used';
    case PATIENT_REFUSED = 'patient_refused';
    case EXPIRED = 'expired';
    case DAMAGED = 'damaged';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::WRONG_ITEM => 'Artículo Incorrecto',
            self::WRONG_QUANTITY => 'Cantidad Incorrecta',
            self::NOT_USED => 'No Usado',
            self::PATIENT_REFUSED => 'Paciente Rechazó',
            self::EXPIRED => 'Vencido',
            self::DAMAGED => 'Dañado',
            self::OTHER => 'Otro',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
