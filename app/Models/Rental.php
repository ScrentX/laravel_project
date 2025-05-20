<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'laptop_id',
        'student_id',
        'rental_date',
        'return_date',
        'due_date',
        'status',
        'condition' 
    ];

    protected $dates = ['rental_date', 'return_date', 'due_date'];

    public function laptop()
    {
        return $this->belongsTo(Laptop::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}