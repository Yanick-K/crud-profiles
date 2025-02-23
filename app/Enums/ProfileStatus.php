<?php

namespace App\Enums;

enum ProfileStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case WAITING = 'waiting';

    /**
     * @return array<int, string>
     */
    public static function values(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
