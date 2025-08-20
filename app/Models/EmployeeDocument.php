<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_detail_id',
        'aadhar_number','aadhar_image',
        'pan_number','pan_image'
    ];

    public function employeeDetail()
    {
        return $this->belongsTo(EmployeeDetail::class);
    }
}
