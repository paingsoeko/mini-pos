<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Unit extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'short_name',
        'unit_category_id',
        'unit_type',
        'value',
        'rounded_amount',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'unit_type' => UnitType::class,
        'value' => 'decimal:4',
        'rounded_amount' => 'decimal:4',
        'deleted_at' => 'datetime',
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

    public function unitCategory()
    {
        return $this->belongsTo(UnitCategory::class);
    }
}
