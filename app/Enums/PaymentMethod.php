<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case ACH = 'ACH';
    case YAPPY = 'YAPPY';
    // case BANK_TRANSFER = 'bank_transfer';
    // case CASH = 'cash';
    // case CREDIT_CARD = 'credit_card';
    // case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ACH => 'ACH',
            self::YAPPY => 'Yappy',
            // self::BANK_TRANSFER => 'ACH',
            // self::CASH => 'Cash',
            // self::CREDIT_CARD => 'Credit Card',
            // self::OTHER => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACH => 'primary',
            self::YAPPY => 'info',
        };
    }

    public function requiresReference(): bool
    {
        return in_array($this, [self::ACH, self::YAPPY]);
    }

    public function info() : array
    {
        return match ($this) {
            self::ACH => ['Banco'=>'Banco General','Cuenta'=>'04-99-99-999999-9','Tipo'=>'Cuenta Corriente','Beneficiario'=>'Soluciones Meditec S.A'],
            self::YAPPY => ['Teléfono'=>'50712345678','Directorio'=>'Soluciones Meditec'],
        };
    }
}
