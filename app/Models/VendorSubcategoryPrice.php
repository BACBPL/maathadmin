<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorSubcategoryPrice extends Model
{
    protected $table = 'vendor_subcategory_prices';
    protected $fillable = ['vendor_id','subcategory_id','price','status'];

    
    protected $casts = [
        'price'  => 'float',
        'status' => 'int',
    ];

    public function vendor()     { return $this->belongsTo(VendorDetail::class, 'vendor_id'); }
    public function subcategory(){ return $this->belongsTo(SubCategory::class, 'subcategory_id'); }
}
