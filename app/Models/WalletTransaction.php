<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['f_id','t_id','amount'];

    public function from()
    {
        return $this->belongsTo(User::class,'f_id');
    }

    public function to()
    {
        return $this->belongsTo(User::class,'t_id');
    }
}
