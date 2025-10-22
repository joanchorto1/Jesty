<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'iva' => $this->faker->randomElement([0, 4, 10, 21]),
            'date' => $this->faker->date(),
            'payment_method_id' => PaymentMethod::factory(),
            'expense_category_id' => ExpenseCategory::factory(),
            'company_id' => Company::factory(),
            'file' => null,
        ];
    }
}
