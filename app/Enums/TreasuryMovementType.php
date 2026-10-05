<?php

namespace App\Enums;

enum TreasuryMovementType: string
{
    case INCOME = 'income';         // Ingreso
    case EXPENSE = 'expense';       // Egreso
    case TRANSFER = 'transfer';     // Transferencia

    public function label(): string
    {
        return match ($this) {
            self::INCOME => 'Ingreso',
            self::EXPENSE => 'Egreso',
            self::TRANSFER => 'Transferencia',
        };
    }
}
