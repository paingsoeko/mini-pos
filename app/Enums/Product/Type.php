<?php

namespace App\Enums\Product;

enum Type: string
{
    case consumable = 'consumable';
    case storable = 'storable';
    case service = 'service';
}
