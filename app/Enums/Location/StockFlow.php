<?php

namespace App\Enums\Location;

enum StockFlow: string
{

    case fifo = 'fifo';
    case lifo = 'lifo';
    case fefo = 'fefo';
}
