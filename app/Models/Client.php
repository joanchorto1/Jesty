<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'name', 'nif', 'bank', 'phone', 'email', 'address'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }
}
