<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'supplier_id',
        'entry_date',
        'reference',
        'base_amount',
        'tax_rate',
        'tax_amount',
        'total',
        'status',
        'notes',
    ];

    /**
     * Relación: Una entrada de stock pertenece a una compañía.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación: Una entrada de stock pertenece a un proveedor.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relación: Una entrada de stock puede tener múltiples ítems.
     */
    public function items()
    {
        return $this->hasMany(StockEntryItem::class);
    }
}
