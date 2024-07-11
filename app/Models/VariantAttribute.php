<?php

namespace App\Models;

use App\Enums\VariantAttributeDisplayType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class VariantAttribute extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'attribute',
        'display_type',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'display_type' => VariantAttributeDisplayType::class,
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

    public function values(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class, 'variant_attribute_id', 'id');
    }
}
