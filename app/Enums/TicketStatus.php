<?php

namespace App\Enums;

enum TicketStatus: string
{
    case open = 'open';
    case in_progress = 'in_progress';
    case on_hold = 'on_hold';

    case resolved = 'resolved';
    case closed = 'closed';


    public function label(): string
    {
        return match ($this) {
            self::open => 'Abierto',
            self::in_progress => 'En Progreso',
            self::on_hold => 'En Espera',
            self::resolved => 'Resuelto',
            self::closed => 'Cerrado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::open => 'primary',
            self::in_progress => 'info',
            self::on_hold => 'warning',
            self::resolved => 'success',
            self::closed => 'danger',
        };
    }

}
