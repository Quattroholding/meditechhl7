<?php

namespace App\Enums;

enum AccountType: string
{
    case ASSET = 'asset';           // Activo
    case LIABILITY = 'liability';   // Pasivo
    case EQUITY = 'equity';         // Patrimonio
    case INCOME = 'income';         // Ingreso
    case EXPENSE = 'expense';       // Gasto
    case COST = 'cost';             // Costo

    public function label(): string
    {
        return match ($this) {
            self::ASSET => 'Activo',
            self::LIABILITY => 'Pasivo',
            self::EQUITY => 'Patrimonio',
            self::INCOME => 'Ingreso',
            self::EXPENSE => 'Gasto',
            self::COST => 'Costo',
        };
    }

    public function isDebitNormal(): bool
    {
        return match ($this) {
            self::ASSET, self::COST, self::EXPENSE => true,
            self::LIABILITY, self::EQUITY, self::INCOME => false,
        };
    }
}
