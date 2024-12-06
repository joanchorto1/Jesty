<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source',
        'tax_base',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'company_id',
        'external_id',
        'date', // Fecha del ingreso
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
