<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $companyFactory = Company::factory();

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'job_title' => $this->faker->jobTitle(),
            'department_id' => Department::factory()->for($companyFactory),
            'salary' => $this->faker->numberBetween(20000, 60000),
            'hire_date' => $this->faker->date(),
            'status' => 'active',
            'company_id' => $companyFactory,
            'dni' => $this->faker->bothify('########?'),
            'nnss' => $this->faker->bothify('###########'),
            'iban' => $this->faker->iban('ES'),
            'birth_date' => $this->faker->date(),
        ];
    }
}
