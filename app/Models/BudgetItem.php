<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id', 'product_id','discount', 'quantity', 'unit_price', 'total'
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
