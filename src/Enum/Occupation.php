<?php

namespace App\Enum;

enum Occupation: string
{
    case Student = 'student';
    case Teacher = 'teacher';
    case Staff = 'staff';
}