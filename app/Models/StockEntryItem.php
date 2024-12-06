<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_entry_id',
        'product_id',
        'quantity',
        'unit_price',
        'total'
    ];

    /**
     * Relación: Un ítem pertenece a una entrada de stock.
     */
    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class);
    }

    /**
     * Relación: Un ítem pertenece a un producto.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
