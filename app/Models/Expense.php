<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'iva',
        'date',
        'payment_method_id',
        'expense_category_id',
        'recurring_expense_id',
        'company_id',
        'external_id',
        'file',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function recurringTemplate()
    {
        return $this->belongsTo(RecurringExpense::class, 'recurring_expense_id');
    }

    public function template()
    {
        return $this->hasOne(RecurringExpense::class, 'id', 'recurring_expense_id');
    }
}
