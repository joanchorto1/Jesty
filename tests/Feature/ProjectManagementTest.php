<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_creation_creates_related_records(): void
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckCompanyPlan::class,
            \App\Http\Middleware\RouteFeaturesAccess::class,
        ]);

        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $department = Department::factory()->create(['company_id' => $company->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $employees = Employee::factory()->count(2)->create([
            'company_id' => $company->id,
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('projects.store'), [
            'name' => 'Proyecto Alpha',
            'status' => 'planning',
            'department_id' => $department->id,
            'client_id' => $client->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'budget' => 15000,
            'description' => 'Proyecto de prueba',
            'phases' => [
                [
                    'name' => 'Descubrimiento',
                    'status' => 'pending',
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addWeeks(2)->toDateString(),
                    'responsible_id' => $employees[0]->id,
                    'notes' => 'Análisis inicial',
                ],
            ],
            'responsibles' => [
                [
                    'employee_id' => $employees[1]->id,
                    'role' => 'Director de proyecto',
                ],
            ],
        ]);

        $response->assertStatus(409);

        $project = Project::with(['phases', 'responsibles'])
            ->where('company_id', $company->id)
            ->first();

        $this->assertNotNull($project);
        $this->assertEquals($department->id, $project->department_id);
        $this->assertEquals($client->id, $project->client_id);
        $this->assertCount(1, $project->phases);
        $this->assertCount(1, $project->responsibles);
        $this->assertEquals($employees[0]->id, $project->phases->first()->responsible_id);
        $this->assertEquals('Director de proyecto', $project->responsibles->first()->role);
    }

    public function test_hr_dashboard_includes_project_metrics(): void
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckCompanyPlan::class,
            \App\Http\Middleware\RouteFeaturesAccess::class,
        ]);

        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $department = Department::factory()->create(['company_id' => $company->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);

        $employee = Employee::factory()->create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'status' => 'active',
        ]);

        $project = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'client_id' => $client->id,
            'status' => 'in_progress',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('dashboard.rrhh'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('DashboardRRHH')
            ->has('projects', fn (Assert $projects) => $projects
                ->where('0.id', $project->id)
                ->where('0.status', 'in_progress')
            )
            ->has('departments', fn (Assert $departments) => $departments
                ->where('0.projects.0.id', $project->id)
            )
            ->has('employees', fn (Assert $employees) => $employees
                ->where('0.department_id', $department->id)
            )
        );
    }
}
