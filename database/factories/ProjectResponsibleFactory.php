<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectResponsible;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectResponsibleFactory extends Factory
{
    protected $model = ProjectResponsible::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'employee_id' => Employee::factory(),
            'role' => $this->faker->jobTitle(),
        ];
    }
}
