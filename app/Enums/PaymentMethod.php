<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case ACH = 'ACH';
    case YAPPY = 'YAPPY';
    case CREDIT_CARD = 'CREDIT_CARD';
    // case BANK_TRANSFER = 'bank_transfer';
    // case CASH = 'cash';
    // case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ACH => 'ACH',
            self::YAPPY => 'Yappy',
            self::CREDIT_CARD => 'Tarjeta de Crédito',
            // self::BANK_TRANSFER => 'ACH',
            // self::CASH => 'Cash',
            // self::OTHER => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACH => 'primary',
            self::YAPPY => 'info',
            self::CREDIT_CARD => 'success',
        };
    }

    public function requiresReference(): bool
    {
        return in_array($this, [self::ACH, self::YAPPY]);
    }

    public function info(): array
    {
        return match ($this) {
            self::ACH => ['Banco' => 'Banco General', 'Cuenta' => '04-99-99-999999-9', 'Tipo' => 'Cuenta Corriente', 'Beneficiario' => 'Soluciones Meditec S.A'],
            self::YAPPY => ['Teléfono' => '50712345678', 'Directorio' => 'Soluciones Meditec'],
            self::CREDIT_CARD => [],
        };
    }
}
