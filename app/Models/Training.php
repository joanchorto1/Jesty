<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date',
        'duration',
    ];

    // Relación con Employee (muchos a muchos)
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_training');
    }
}
