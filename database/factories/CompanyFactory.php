<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'nif' => strtoupper($this->faker->bothify('??########')),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
            'address' => $this->faker->address(),
            'plan_id' => null,
            'public_key' => $this->faker->uuid(),
            'private_key' => $this->faker->uuid(),
        ];
    }
}
