<?php

namespace App;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Couple = 'couple';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
            self::Couple => 'Couple',
            self::Other => 'Other',
        };
    }
}
