<?php

namespace App\Enums;

enum DocumentType: string
{
    case INVENTORY = 'inventory';
    case ENSA = 'ensa';

    case NATURGY = 'naturgy';

    case IDAAN = 'idaan';

    case OTRO = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::INVENTORY => 'INVENTARIO',
            self::ENSA => 'ENSA',
            self::NATURGY => 'NATURGY',
            self::IDAAN => 'IDAAN',
            self::OTRO => 'OTRO',
        };
    }
}
