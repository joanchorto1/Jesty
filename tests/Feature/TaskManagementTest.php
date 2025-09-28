<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_task_can_be_created_for_a_lead(): void
    {
        $company = Company::create([
            'name' => 'Test Company',
            'nif' => 'A12345678',
            'phone' => '123456789',
            'email' => 'company@example.com',
            'address' => 'Address 123',
        ]);
        $user = User::factory()->create(['company_id' => $company->id]);

        $lead = Lead::create([
            'name' => 'Lead Test',
            'company_name' => 'Company',
            'email' => Str::random(10) . '@example.com',
            'phone' => '123456789',
            'position' => 'Manager',
            'source' => 'web',
            'status' => 'Nuevo',
            'company_id' => $company->id,
        ]);

        $this->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => 'Follow up call',
                'description' => 'Call the lead tomorrow',
                'due_date' => now()->addDay()->toDateString(),
                'status' => 'pendiente',
                'taskable_type' => 'lead',
                'taskable_id' => $lead->id,
            ])
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'title' => 'Follow up call',
            'taskable_type' => Lead::class,
            'taskable_id' => $lead->id,
            'company_id' => $company->id,
        ]);
    }

    /** @test */
    public function a_task_can_be_updated_to_target_an_opportunity(): void
    {
        $company = Company::create([
            'name' => 'Another Company',
            'nif' => 'B12345678',
            'phone' => '987654321',
            'email' => 'another@example.com',
            'address' => 'Other address',
        ]);
        $user = User::factory()->create(['company_id' => $company->id]);

        $lead = Lead::create([
            'name' => 'Lead Update',
            'company_name' => 'Company',
            'email' => Str::random(10) . '@example.com',
            'phone' => '987654321',
            'position' => 'CTO',
            'source' => 'referral',
            'status' => 'Nuevo',
            'company_id' => $company->id,
        ]);

        $opportunity = Opportunity::create([
            'lead_id' => $lead->id,
            'description' => 'New opportunity',
            'value' => 1000,
            'probability' => 50,
            'status' => 'Abierta',
            'expected_close_date' => now()->addWeek()->toDateString(),
            'company_id' => $company->id,
        ]);

        $task = Task::create([
            'title' => 'Initial task',
            'description' => 'Initial description',
            'due_date' => now()->addDays(2)->toDateString(),
            'status' => 'pendiente',
            'company_id' => $company->id,
            'taskable_type' => Lead::class,
            'taskable_id' => $lead->id,
        ]);

        $this->actingAs($user)
            ->put(route('tasks.update', $task->id), [
                'title' => 'Updated task',
                'description' => 'Updated description',
                'due_date' => now()->addDays(3)->toDateString(),
                'status' => 'en progreso',
                'taskable_type' => 'opportunity',
                'taskable_id' => $opportunity->id,
            ])
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated task',
            'taskable_type' => Opportunity::class,
            'taskable_id' => $opportunity->id,
            'status' => 'en progreso',
        ]);
    }
}
