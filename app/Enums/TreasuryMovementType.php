<?php

namespace App\Enums;

enum TreasuryMovementType: string
{
    case DEPOSIT = 'deposit';           // Depósito/Ingreso
    case WITHDRAWAL = 'withdrawal';     // Retiro/Egreso
    case TRANSFER = 'transfer';         // Transferencia
    case ADJUSTMENT = 'adjustment';     // Ajuste

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => 'Depósito',
            self::WITHDRAWAL => 'Retiro',
            self::TRANSFER => 'Transferencia',
            self::ADJUSTMENT => 'Ajuste',
        };
    }
}
