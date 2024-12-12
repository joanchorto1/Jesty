<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'base_salary',
        'bonuses',
        'deductions',
        'net_pay',
        'pay_date',
        'status',
    ];

    // Relación con Employee (muchos a uno)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
