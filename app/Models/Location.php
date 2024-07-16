<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Location extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'type',
        'stock_flow',
        'parent_location_id',
    ];
    public function parent_location() :HasOne{
        return $this->hasOne(Location::class,'id','parent_location_id');
    }
}
