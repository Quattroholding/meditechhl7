<?php

namespace App\Enums;

enum JournalEntryStatus: string
{
    case DRAFT = 'draft';           // Borrador
    case POSTED = 'posted';         // Contabilizado
    case REVERSED = 'reversed';     // Revertido

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::POSTED => 'Contabilizado',
            self::REVERSED => 'Revertido',
        };
    }
}
