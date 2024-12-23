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
        'company_id',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_training', 'training_id', 'employee_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
