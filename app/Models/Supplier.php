<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'address',
    ];

    /**
     * Relación: Un proveedor pertenece a una compañía.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación: Un proveedor puede tener muchos productos.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relación: Un proveedor puede tener muchas entradas de stock.
     */
    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }
}
