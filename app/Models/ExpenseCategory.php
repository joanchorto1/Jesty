<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
