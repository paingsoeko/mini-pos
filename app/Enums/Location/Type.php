<?php

namespace App\Enums\Location;


enum Type : string
{
    case virtual = 'virtual';
    case view = 'view';
    case internal = 'internal';
}
