<?php

namespace App\Http\Enum;

enum UserActionsEnum: int
{
    case LIKE = 1;
    case FAVOURITE = 2;

    public static function getUserActionValues(): array
    {
        return array_map(fn($actions) => $actions->value, self::cases());
    }
}
