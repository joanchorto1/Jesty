<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\RecurringExpense;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RecurringExpenseFactory extends Factory
{
    protected $model = RecurringExpense::class;

    public function definition(): array
    {
        $start = Carbon::now()->addDay();

        return [
            'company_id' => Company::factory(),
            'expense_category_id' => ExpenseCategory::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'iva' => $this->faker->randomElement([0, 4, 10, 21]),
            'frequency_type' => $this->faker->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'frequency_interval' => $this->faker->numberBetween(1, 3),
            'next_run_at' => $start,
            'ends_at' => null,
            'last_generated_at' => Carbon::now(),
            'status' => 'active',
        ];
    }
}
