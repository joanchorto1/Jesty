<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Transferencia', 'Tarjeta', 'Efectivo', 'PayPal']),
            'description' => $this->faker->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
