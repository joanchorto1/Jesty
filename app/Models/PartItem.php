<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
        'iva',
    ];

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
