<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case PENDING = 'pending';
    case PARSING = 'parsing';
    case PARSED = 'parsed';
    case PARSING_FAILED = 'parsing-failed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case PROCESSING_FAILED = 'processing-failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PARSING => 'Procesando',
            self::PARSED => 'Procesado',
            self::PARSING_FAILED => 'Error de Procesamiento',
            self::APPROVED => 'Aprobado',
            self::REJECTED => 'Rechazado',
            self::PROCESSING => 'Procesando Datos',
            self::PROCESSED => 'Completado',
            self::PROCESSING_FAILED => 'Error en Procesamiento',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PARSING => 'yellow',
            self::PARSED => 'blue',
            self::PARSING_FAILED => 'red',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::PROCESSING => 'yellow',
            self::PROCESSED => 'green',
            self::PROCESSING_FAILED => 'red',
        };
    }
}
