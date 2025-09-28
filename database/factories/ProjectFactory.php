<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $companyFactory = Company::factory();

        return [
            'company_id' => $companyFactory,
            'department_id' => Department::factory()->for($companyFactory),
            'client_id' => Client::factory()->for($companyFactory),
            'name' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement(['planning', 'in_progress', 'completed']),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'budget' => $this->faker->randomFloat(2, 1000, 50000),
            'description' => $this->faker->paragraph(),
        ];
    }
}
