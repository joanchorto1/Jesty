<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'review_date',
        'score',
        'comments',
        'reviewed_by',
    ];

    // Relación con Employee (muchos a uno como evaluado)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Relación con Employee (muchos a uno como evaluador)
    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }
}
