<?php

namespace App\Helper;

use InvalidArgumentException;
use Illuminate\Validation\Rules\Enum;

class Helper
{
    public static function getEnumValues($enumClass): array {
        return array_column($enumClass, 'value');
    }
}
