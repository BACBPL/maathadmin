<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','image','based'];

     public function subCategories()
    {
        // table: sub_categories, fk: category_id
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}
