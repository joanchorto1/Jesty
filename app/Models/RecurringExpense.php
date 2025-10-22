<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'expense_category_id',
        'payment_method_id',
        'name',
        'description',
        'amount',
        'iva',
        'frequency_type',
        'frequency_interval',
        'next_run_at',
        'ends_at',
        'last_generated_at',
        'status',
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_generated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
