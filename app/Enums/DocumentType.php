<?php

namespace App\Enums;

enum DocumentType: string
{
    case INVENTORY = 'inventory';
    case ELECTRICITY_BILL = 'electricity-bill';
    case WATER_BILL = 'water-bill';
    case GAS_BILL = 'gas-bill';

    public function label(): string
    {
        return match ($this) {
            self::INVENTORY => 'Inventario',
            self::ELECTRICITY_BILL => 'Factura Luz',
            self::WATER_BILL => 'Factura Agua',
            self::GAS_BILL => 'Factura Gas',
        };
    }
}
