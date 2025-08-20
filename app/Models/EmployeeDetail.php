<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    use HasFactory;

     protected $fillable = [
        'name', 'address', 'phone','password','email','photo'
    ];

     public function documents()
    {
        return $this->hasOne(EmployeeDocument::class);
    }
}
