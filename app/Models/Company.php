<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'nif', 'phone', 'email', 'address', 'public_key', 'private_key'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
}
