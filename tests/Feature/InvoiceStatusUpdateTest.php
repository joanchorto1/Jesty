<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckCompanyPlan;
use App\Http\Middleware\RouteFeaturesAccess;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_status_can_be_updated_via_new_route(): void
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

        $invoice = Invoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'name' => 'FA-0001',
            'date' => now()->toDateString(),
            'state' => 'pending',
            'base_imponible' => 100,
            'iva' => 21,
            'monto_iva' => 21,
            'total' => 121,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson(route('invoices.updateStatus', $invoice), ['state' => 'paid']);

        $response->assertOk()
            ->assertJsonPath('invoice.state', 'paid');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'state' => 'paid',
        ]);
    }
}
