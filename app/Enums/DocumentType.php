<?php

namespace App\Enums;

enum DocumentType: string
{
    case INVENTORY = 'inventory';
    case INVENTORY_AI = 'inventory_ai';
    case ENSA = 'ensa';

    case NATURGY = 'naturgy';

    case IDAAN = 'idaan';

    case OTRO = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::INVENTORY => 'INVENTARIO (Regex)',
            self::INVENTORY_AI => 'INVENTARIO (IA)',
            self::ENSA => 'ENSA',
            self::NATURGY => 'NATURGY',
            self::IDAAN => 'IDAAN',
            self::OTRO => 'OTRO',
        };
    }
}
