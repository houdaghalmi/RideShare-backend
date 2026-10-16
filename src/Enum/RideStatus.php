<?php

namespace App\Enum;

enum RideStatus: string
{
    case Open = 'open';
    case Full = 'full';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
