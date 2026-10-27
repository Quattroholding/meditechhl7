<?php

namespace App\Enums\Accounting;

enum JournalEntryDocumentType: string
{
    case INVOICE = 'invoice';
    case PAYMENT = 'payment';
    case PAYMENT_SCHEDULE = 'payment_schedule';
    case SUPPLIER_INVOICE = 'supplier_invoice';
    case TREASURY_MOVEMENT = 'treasury_movement';
    case DEBIT_NOTE = 'debit_note';
    case CREDIT_NOTE = 'credit_note';
    case PURCHASE_ORDER = 'purchase_order';
    case MANUAL = 'manual';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::INVOICE => 'Factura',
            self::PAYMENT => 'Pago',
            self::PAYMENT_SCHEDULE => 'Programación de Pago',
            self::SUPPLIER_INVOICE => 'Factura de Proveedor',
            self::TREASURY_MOVEMENT => 'Movimiento de Tesorería',
            self::DEBIT_NOTE => 'Nota de Débito',
            self::CREDIT_NOTE => 'Nota de Crédito',
            self::PURCHASE_ORDER => 'Orden de Compra',
            self::MANUAL => 'Manual',
            self::OTHER => 'Otro',
        };
    }
}
