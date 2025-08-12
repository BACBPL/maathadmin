<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_detail_id',
        'aadhar_number','aadhar_image',
        'pan_number','pan_image',
        'trade_license_number','trade_license_image',
        'gst_number','gst_image'
    ];

    public function vendorDetail()
    {
        return $this->belongsTo(VendorDetail::class);
    }
}
