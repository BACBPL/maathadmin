<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityPincode extends Model
{
    protected $table = 'city_pincodes';
    public $timestamps = true;

    protected $fillable = ['city', 'pincode'];
}
