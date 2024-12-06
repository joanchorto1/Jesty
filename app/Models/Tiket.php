<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_imponible',
        'iva',
        'monto_iva',
        'total',
        'company_id',
    ];

    // Relación con TiketItem
    public function items()
    {
        return $this->hasMany(TiketItem::class);
    }

    // Relación con la compañía
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
