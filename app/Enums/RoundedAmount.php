<?php

namespace App\Enums;

enum RoundedAmount: int
{
    case One = 1;
    case Two = 2;
    case Three = 3;
    case Four = 4;

    public static function labels(): array
    {
        return [
            1 => 'One (.0)',
            2 => 'Two (.00)',
            3 => 'Three (.000)',
            4 => 'Four (.0000)',
        ];
    }
}
