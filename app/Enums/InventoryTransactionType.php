<?php

namespace App\Enums;

enum InventoryTransactionType: string
{
    case PURCHASE = 'purchase';
    case ADJUSTMENT = 'adjustment';
    case TRANSFER = 'transfer';
    case SUPPLY_DELIVERY = 'supply_delivery';
    case RETURN = 'return';
    case DISPOSAL = 'disposal';
    case COUNT = 'count';
}
