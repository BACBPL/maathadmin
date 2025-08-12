<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletDetail extends Model
{
    use HasFactory;

    protected $fillable = ['user','wallet_balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
