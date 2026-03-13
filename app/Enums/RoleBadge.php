<?php

namespace App\Enums;

enum RoleBadge: int
{
    case Admin = 1;
    case Doctor = 2;
    case Recepcionista = 3;
    case Paciente = 4;
    case AdminClient = 5;
    case AsistenteMedico = 6;
    case Contabilidad = 7;
    case Validador = 8;
    case Soporte = 9;

    public function badgeClass(): string
    {
        return match ($this) {
            self::Admin => 'bg-dark',
            self::Doctor => 'bg-primary',
            self::Recepcionista => 'bg-success',
            self::Paciente => 'bg-info',
            self::AdminClient => 'bg-warning',
            self::AsistenteMedico => 'bg-danger',
            self::Contabilidad => 'bg-purple-light',
            self::Validador => 'bg-secondary',
            self::Soporte => 'bg-orange',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'admin',
            self::Doctor => 'doctor',
            self::Recepcionista => 'recepcionista',
            self::Paciente => 'paciente',
            self::AdminClient => 'admin client',
            self::AsistenteMedico => 'asistente medico',
            self::Contabilidad => 'contabilidad',
            self::Validador => 'validador',
            self::Soporte => 'soporte',
        };
    }

    public static function fromId(int $id): ?self
    {
        return self::tryFrom($id);
    }
}
