<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckCompanyPlan;
use App\Http\Middleware\RouteFeaturesAccess;
use App\Models\Budget;
use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_status_can_be_updated_via_new_route(): void
    {
        $this->withoutMiddleware([
            RouteFeaturesAccess::class,
            CheckCompanyPlan::class,
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'nif' => 'A12345678',
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        $client = Client::create([
            'company_id' => $company->id,
            'name' => 'Client Test',
            'nif' => 'B12345678',
            'email' => 'client@example.com',
        ]);

        $budget = Budget::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'name' => 'PR-0001',
            'date' => now()->toDateString(),
            'state' => 'in_process',
            'base_imponible' => 100,
            'iva' => 21,
            'monto_iva' => 21,
            'total' => 121,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson(route('budgets.updateStatus', $budget), ['state' => 'accepted']);

        $response->assertOk()
            ->assertJsonPath('budget.state', 'accepted');

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'state' => 'accepted',
        ]);
    }
}
