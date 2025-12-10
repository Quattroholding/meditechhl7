<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case SINGLE = 'Soltero/a';
    case MARRIED = 'Casado/a';
    case DIVORCED = 'Divorciado/a';
    case WIDOWED = 'Viudo/a';

    /**
     * Obtener el label (el mismo value en este caso)
     */
    public function label(): string
    {
        return $this->value;
    }

    /**
     * Obtener todos los estados civiles como array para selects
     * Compatible con el formato de Lista::maritalStatus()
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'Soltero/a' => 'Soltero/a',
            'Casado/a' => 'Casado/a',
            'Divorciado/a' => 'Divorciado/a',
            'Viudo/a' => 'Viudo/a',
        ];
    }

    /**
     * Obtener el icono asociado
     */
    public function icon(): string
    {
        return match ($this) {
            self::SINGLE => 'fa-user',
            self::MARRIED => 'fa-ring',
            self::DIVORCED => 'fa-user-slash',
            self::WIDOWED => 'fa-heart-broken',
        };
    }

    /**
     * Crear desde un string
     */
    public static function fromString(?string $value): ?self
    {
        if (empty($value)) {
            return null;
        }

        $value = strtolower(trim($value));

        return match (true) {
            str_contains($value, 'solter') => self::SINGLE,
            str_contains($value, 'casad') => self::MARRIED,
            str_contains($value, 'divorciad') => self::DIVORCED,
            str_contains($value, 'viud') => self::WIDOWED,
            default => null,
        };
    }
}
