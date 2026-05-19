<?php

namespace App\Enums;

enum InventoryItemType: string
{
    case SUPPLY = 'supply';
    case CONSUMABLE = 'consumable';
    case EQUIPMENT = 'equipment';
    case MEDICATION = 'medication';
    case DEVICE = 'device';

    /**
     * Obtener el label traducido del tipo de item
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPPLY => 'Suministro',
            self::CONSUMABLE => 'Consumible',
            self::EQUIPMENT => 'Equipo',
            self::MEDICATION => 'Medicamento',
            self::DEVICE => 'Dispositivo',
        };
    }

    /**
     * Obtener todos los tipos de items como array para selects
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::SUPPLY->value => 'Suministro',
            self::CONSUMABLE->value => 'Consumible',
            self::EQUIPMENT->value => 'Equipo',
            self::MEDICATION->value => 'Medicamento',
            self::DEVICE->value => 'Dispositivo',
        ];
    }
}
