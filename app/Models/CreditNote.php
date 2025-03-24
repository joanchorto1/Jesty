<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'total_without_tax', 'tax_amount', 'total_with_tax'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creditNoteItems()
    {
        return $this->hasMany(CreditNoteItem::class);
    }
}
