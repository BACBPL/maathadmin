<?php

// app/Models/VendorService.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorService extends Model
{
    protected $fillable = ['vendor_id', 'subcategory_ids'];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorDetail::class, 'vendor_id');
    }

    public function subcategoryIdArray(): array
    {
        if (!$this->subcategory_ids) return [];
        $ids = array_filter(explode('-', $this->subcategory_ids), fn($v) => $v !== '');
        $ids = array_map('intval', $ids);
        $ids = array_values(array_unique($ids));
        sort($ids);
        return $ids;
    }
}

