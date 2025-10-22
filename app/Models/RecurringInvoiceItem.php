<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recurring_invoice_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
        'iva',
    ];

    protected $casts = [
        'quantity' => 'int',
        'unit_price' => 'float',
        'discount' => 'float',
        'iva' => 'float',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(RecurringInvoice::class, 'recurring_invoice_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
