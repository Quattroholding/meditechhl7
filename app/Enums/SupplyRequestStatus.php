<?php

namespace App\Enums;

enum SupplyRequestStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case ENTERED_IN_ERROR = 'entered-in-error';
    case UNKNOWN = 'unknown';
}
