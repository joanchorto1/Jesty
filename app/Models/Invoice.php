<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'recurring_invoice_id',
        'client_id',
        'company_id',
        'date',
        'due_date',
        'name',
        'number',
        'external_reference',
        'base_imponible',
        'iva',
        'monto_iva',
        'total',
        'state',
        'notes',
        'irpf_tax',
        'total_irpf',
        'pdf_path',
        'public_token',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'base_imponible' => 'float',
        'iva' => 'float',
        'monto_iva' => 'float',
        'total' => 'float',
        'irpf_tax' => 'float',
        'total_irpf' => 'float',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function recurringTemplate()
    {
        return $this->belongsTo(RecurringInvoice::class, 'recurring_invoice_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
