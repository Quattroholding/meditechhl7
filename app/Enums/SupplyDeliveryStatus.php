<?php

namespace App\Enums;

enum SupplyDeliveryStatus: string
{
    case IN_PROGRESS = 'in-progress';
    case COMPLETED = 'completed';
    case ABANDONED = 'abandoned';
    case ENTERED_IN_ERROR = 'entered-in-error';
}
