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

    /**
     * Get supplier data for known utility companies
     * Returns null for non-utility documents (INVENTORY, OTRO)
     */
    public function getSupplierData(): ?array
    {
        return match ($this) {
            self::IDAAN => [
                'legal_name' => 'Instituto de Acueductos y Alcantarillados Nacionales',
                'commercial_name' => 'IDAAN',
                'ruc' => '8NT110284',
                'dv' => '85',
                'address' => 'Vía Brasil, Calle Juan Rivera Reyes, Edificio IDAAN, Ciudad de Panamá',
                'phone' => null,
                'email' => null,
            ],
            self::ENSA => [
                'legal_name' => 'Electra Noreste, S.A.',
                'commercial_name' => 'ENSA',
                'ruc' => '5798356340439',
                'dv' => '65',
                'address' => 'Centro Comercial Plaza Carolina, Local 18, Avenida Ricardo J. Alfaro, Betania, Panamá',
                'phone' => null,
                'email' => null,
            ],
            self::NATURGY => [
                'legal_name' => 'NATURGY PANAMÁ S.A.',
                'commercial_name' => 'NATURGY',
                'ruc' => '2588862832056',
                'dv' => '20',
                'address' => null,
                'phone' => null,
                'email' => null,
            ],
            default => null, // INVENTORY and OTRO don't have predefined supplier data
        };
    }

    /**
     * Check if this document type has predefined supplier data
     */
    public function isKnownUtility(): bool
    {
        return $this->getSupplierData() !== null;
    }
}
