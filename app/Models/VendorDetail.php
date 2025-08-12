<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'phone','password','email', 'company_name','photo'
    ];

    public function documents()
    {
        return $this->hasOne(VendorDocument::class);
    }

     public function walletDetail()
    {
        return $this->hasOne(WalletDetail::class, 'user', 'id');
    }

    public function services(): HasOne
    {
        return $this->hasOne(VendorService::class, 'vendor_id');
    }
}
