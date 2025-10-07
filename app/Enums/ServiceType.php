<?php

namespace App\Enums;

enum ServiceType: string
{
    case LABORATORY = 'laboratory';
    case RADIOLOGY = 'radiology';
    case IMAGING = 'imaging';
    case PATHOLOGY = 'pathology';
    case PROCEDURE = 'procedure';
    case THERAPY = 'therapy';
    case CONSULTATION = 'consultation';
    case OTHER = 'other';

    /**
     * Obtiene el nombre en español del tipo de servicio
     */
    public function label(): string
    {
        return match ($this) {
            self::LABORATORY => 'Laboratorio',
            self::RADIOLOGY => 'Radiología',
            self::IMAGING => 'Imagenología',
            self::PATHOLOGY => 'Patología',
            self::PROCEDURE => 'Procedimiento',
            self::THERAPY => 'Terapia',
            self::CONSULTATION => 'Consulta',
            self::OTHER => 'Otros Servicios',
        };
    }

    /**
     * Obtiene el nombre en plural en español del tipo de servicio
     */
    public function pluralLabel(): string
    {
        return match ($this) {
            self::LABORATORY => 'Laboratorios',
            self::RADIOLOGY => 'Radiología',
            self::IMAGING => 'Imagenología',
            self::PATHOLOGY => 'Patología',
            self::PROCEDURE => 'Procedimientos',
            self::THERAPY => 'Terapias',
            self::CONSULTATION => 'Consultas',
            self::OTHER => 'Otros Servicios',
        };
    }

    /**
     * Obtiene todos los tipos de servicios como un array para select/dropdown
     */
    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    /**
     * Obtiene el label desde un string value
     */
    public static function labelFromValue(?string $value): string
    {
        if (! $value) {
            return 'Servicios Médicos';
        }

        return self::tryFrom($value)?->label() ?? ucfirst($value);
    }

    /**
     * Obtiene el plural label desde un string value
     */
    public static function pluralLabelFromValue(?string $value): string
    {
        if (! $value) {
            return 'Servicios Médicos';
        }

        return self::tryFrom($value)?->pluralLabel() ?? ucfirst($value);
    }
}
