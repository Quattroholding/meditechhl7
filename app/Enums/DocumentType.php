<?php

namespace App\Enums;

enum DocumentType: string
{
    case INVENTORY = 'inventory';
    case ELECTRICITY_BILL = 'electricity-bill';
    case WATER_BILL = 'water-bill';
    case GAS_BILL = 'gas-bill';

    case ENSA = 'ensa';

    case NATURGY = 'naturgy';

    case IDAAN = 'idaan';

    case OTRO = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::INVENTORY => 'INVENTARIO',
            self::ELECTRICITY_BILL => 'FACTURA DE ELECTRICIDAD',
            self::WATER_BILL => 'FACTURA DE AGUA',
            self::GAS_BILL => 'FACTURA DE GAS',
            self::ENSA => 'ENSA',
            self::NATURGY => 'NATURGY',
            self::IDAAN => 'IDAAN',
            self::OTRO => 'OTRO',
        };
    }
}
