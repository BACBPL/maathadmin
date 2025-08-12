<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorArea extends Model
{
    protected $table = 'vendor_area';
    protected $fillable = ['v_id', 'service_area','verified'];


    public function vendor()
    {
        return $this->belongsTo(\App\Models\VendorDetail::class, 'v_id');
    }
}
