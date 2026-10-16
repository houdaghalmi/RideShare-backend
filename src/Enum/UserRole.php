<?php


namespace App\Enum;


enum UserRole: string
{
    case Admin = 'ROLE_ADMIN';
    case Driver = 'ROLE_DRIVER';
    case Passenger = 'ROLE_PASSENGER';
    case Both = 'ROLE_BOTH';

    /** Roles a user may pick for themselves (e.g. in the registration form). */
    public static function selectable(): array
    {
        return [self::Passenger, self::Driver, self::Both];
    }
}