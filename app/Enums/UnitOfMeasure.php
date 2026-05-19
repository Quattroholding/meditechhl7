<?php

namespace App\Enums;

enum UnitOfMeasure: string
{
    case UNIT = 'unit';
    case BOX = 'box';
    case VIAL = 'vial';
    case MILLILITER = 'ml';
    case MILLIGRAM = 'mg';
    case GRAM = 'g';

    /**
     * Obtener el label traducido de la unidad de medida
     */
    public function label(): string
    {
        return match ($this) {
            self::UNIT => 'Unidad',
            self::BOX => 'Caja',
            self::VIAL => 'Vial',
            self::MILLILITER => 'Mililitro',
            self::MILLIGRAM => 'Miligramo',
            self::GRAM => 'Gramo',
        };
    }

    /**
     * Obtener todas las unidades de medida como array para selects
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::UNIT->value => 'Unidad',
            self::BOX->value => 'Caja',
            self::VIAL->value => 'Vial',
            self::MILLILITER->value => 'Mililitro',
            self::MILLIGRAM->value => 'Miligramo',
            self::GRAM->value => 'Gramo',
        ];
    }

    /**
     * Obtener símbolo corto de la unidad
     */
    public function symbol(): string
    {
        return match ($this) {
            self::UNIT => 'ud',
            self::BOX => 'caja',
            self::VIAL => 'vial',
            self::MILLILITER => 'ml',
            self::MILLIGRAM => 'mg',
            self::GRAM => 'g',
        };
    }
}
