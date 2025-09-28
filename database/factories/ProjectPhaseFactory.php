<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectPhaseFactory extends Factory
{
    protected $model = ProjectPhase::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => $this->faker->words(2, true),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+3 months')->format('Y-m-d'),
            'responsible_id' => Employee::factory(),
            'notes' => $this->faker->sentence(),
        ];
    }
}
