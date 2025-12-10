<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    /**
     * Obtener el label traducido del género
     */
    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Masculino',
            self::FEMALE => 'Femenino',
        };
    }

    /**
     * Obtener todos los géneros como array para selects
     * Compatible con el formato de Lista::gender()
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'male' => 'Masculino',
            'female' => 'Femenino',
        ];
    }

    /**
     * Obtener el prefijo de título (Dr./Dra.)
     */
    public function titlePrefix(): string
    {
        return match ($this) {
            self::FEMALE => 'Dra. ',
            self::MALE => 'Dr. ',
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

        return match ($value) {
            'male', 'm', 'masculino', 'hombre' => self::MALE,
            'female', 'f', 'femenino', 'mujer' => self::FEMALE,
            default => null,
        };
    }
}
