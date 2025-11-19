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
        'date',
        'due_date',
        'name',
        'base_imponible',
        'iva',
        'monto_iva',
        'total',
        'state',
        'notes',
        'irpf_tax',
        'total_irpf',
        'company_id',
        'pdf_path',
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
