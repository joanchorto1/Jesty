<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'iva' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'file' => ['nullable', 'file'],
            'recurring.enabled' => ['sometimes', 'boolean'],
            'recurring.id' => ['nullable', 'exists:recurring_expenses,id'],
            'recurring.frequency_type' => [
                'nullable',
                Rule::requiredIf(fn () => $this->boolean('recurring.enabled')),
                Rule::in(['daily', 'weekly', 'monthly', 'yearly']),
            ],
            'recurring.frequency_interval' => [
                'nullable',
                Rule::requiredIf(fn () => $this->boolean('recurring.enabled')),
                'integer',
                'min:1',
            ],
            'recurring.next_run_at' => [
                'nullable',
                Rule::requiredIf(fn () => $this->boolean('recurring.enabled')),
                'date',
            ],
            'recurring.ends_at' => ['nullable', 'date', 'after:recurring.next_run_at'],
            'recurring.status' => ['nullable', Rule::in(['active', 'paused', 'inactive'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $recurring = $this->input('recurring', []);

        if (is_array($recurring)) {
            $recurring['enabled'] = filter_var($recurring['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $this->merge(['recurring' => $recurring]);
        }
    }
}
