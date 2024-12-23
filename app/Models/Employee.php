<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'job_title',
        'department_id',
        'salary',
        'hire_date',
        'status',
        'company_id',
        'dni', // Nuevo campo
        'nnss', // Nuevo campo (Número de Seguridad Social)
        'iban', // Nuevo campo
        'birth_date', // Nuevo campo
    ];

    // Relación con Department (muchos a uno)
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relación con Payroll (uno a muchos)
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    // Relación con Attendance (uno a muchos)
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Relación con Leave (uno a muchos)
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    // Relación con PerformanceReview (uno a muchos como evaluado)
    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }

    // Relación con PerformanceReview (uno a muchos como evaluador)
    public function reviewsGiven()
    {
        return $this->hasMany(PerformanceReview::class, 'reviewed_by');
    }

    // Relación con Training (muchos a muchos)
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'employee_training');
    }

    // Relación con Company (muchos a uno)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
