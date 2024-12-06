<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
    ];

    // Relación con el Tiket
    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
