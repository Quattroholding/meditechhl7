<?php

namespace App\Enums;

enum ClientType
{
    case CONSULTA_PRIVADA;

    case CENTRO_ATENCION_PRIMARIO;
    case CLINICA;
    case HOSPITAL;
    case OTHER ;

    public function label(): string
    {
        return match ($this) {
            self::CONSULTA_PRIVADA => 'Consultorio Privado',
            self::CENTRO_ATENCION_PRIMARIO => 'Centro de Atencion Primario',
            self::CLINICA => 'Clinica',
            self::HOSPITAL => 'Hospital',
            self::OTHER => 'Otro',
        };
    }
}
