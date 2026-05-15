<?php

namespace App\Enums;

enum InventoryItemStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ENTERED_IN_ERROR = 'entered-in-error';
    case UNKNOWN = 'unknown';
}
