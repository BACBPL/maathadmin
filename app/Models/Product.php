<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};

class Product extends Model
{
    protected $fillable = [
        'vendor_id','title','slug','sku','price','sale_price','stock_qty',
        'status','description','weight','length','width','height'
    ];

    public function images(): HasMany {
        return $this->hasMany(ProductImage::class);
    }

    public function specs(): HasMany {
        return $this->hasMany(ProductSpec::class);
    }

    public function categories(): BelongsToMany {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function primaryImage() {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public static function getProductImage($product_id) { // this method is used in front/orders/order_details.blade.php
        return \App\Models\ProductImage::where('product_id', $product_id)
               ->orderByDesc('is_primary')
               ->value('path') ?: 'no_image.png';
    }
}
