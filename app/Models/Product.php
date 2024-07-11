<?php

namespace App\Models;

use App\Enums\Product\Type;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'sku',
        'product_code',
        'name',
        'image',
        'type',
        'sales_price',
        'cost',
        'product_category_id',
        'unit_id',
        'purchase_unit_id',
        'internal_notes',
        'can_be_sold',
        'can_be_purchased',
        'security_stock'.
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'sales_price' => 'decimal:4',
        'cost' => 'decimal:4',
        'can_be_sold' => 'boolean',
        'can_be_purchased' => 'boolean',
        'type' => Type::class,
    ];

    public static function generateSku($id)
    {
        return str_pad($id, 6, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });


        static::created(function ($product) {
            if (empty($product->sku)) {
                $product->sku = self::generateSku($product->id);
                $product->save();
            }
        });



        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

    }

    // Define relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchaseUnit()
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag', 'product_id', 'tag_id');
    }



//    public function tags()
//    {
//        return $this->belongsToMany(Tag::class);
//    }

//    public function productCategory()
//    {
//        return $this->belongsTo(ProductCategory::class);
//    }

//    public function variantTemplateValues()
//    {
//        return $this->belongsToMany(VariantTemplateValue::class, 'product_variant');
//    }



//
//    public function purchase_details()
//    {
//        return $this->hasMany(PurchaseDetail::class, 'product_id', 'id');
//    }
//
//    public function sale_details()
//    {
//        return $this->hasMany(SaleDetail::class, 'product_id', 'id');
//    }
//
//
//    // Define the relationship for created_by
//    public function creator()
//    {
//        return $this->belongsTo(User::class, 'created_by');
//    }
//
//    // Define the relationship for updated_by
//    public function updater()
//    {
//        return $this->belongsTo(User::class, 'updated_by');
//    }
}
