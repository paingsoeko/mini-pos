<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class VariantAttributeValue extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'value',
        'variant_attribute_id',
        'color_code',
        'image',
        'default_extra_price',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'default_extra_price' => 'decimal:4',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

    }

    public function attribute()
    {
        return $this->belongsTo(VariantAttribute::class, 'variant_attribute_id');
    }
}
