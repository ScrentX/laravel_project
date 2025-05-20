<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'condition',
        'serial_number',
        'status',
        'acquired_date', 
        'image_path'
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}