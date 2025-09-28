<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manager_id',
        'company_id',
    ];

    // Relación con Employee (uno a muchos)
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Relación con Employee (uno a uno para gerente)
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    // Relación con Company (muchos a uno)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
