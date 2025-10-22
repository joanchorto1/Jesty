<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\RecurringInvoice;
use App\Models\RecurringInvoiceItem;
use App\Models\User;
use App\Services\RecurringInvoiceGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringInvoicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
    }

    public function test_user_can_create_recurring_template_and_generate_first_invoice(): void
    {
        $company = Company::create([
            'name' => 'Test Company',
            'nif' => 'A12345678',
            'phone' => '123456789',
            'email' => 'company@example.com',
            'address' => 'Calle Falsa 123',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name' => 'Usuario Demo',
            'email' => 'demo@example.com',
            'password' => bcrypt('password'),
            'phone' => '999999999',
            'address' => 'Dirección demo',
            'role_id' => null,
        ]);

        $category = Category::create([
            'company_id' => $company->id,
            'name' => 'Servicios',
            'description' => 'Servicios recurrentes',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'name' => 'Mantenimiento',
            'description' => 'Servicio mensual',
            'price' => 100,
            'iva' => 21,
            'stock' => 10,
            'is_stackable' => true,
            'disabled' => false,
        ]);

        $client = Client::create([
            'company_id' => $company->id,
            'name' => 'Cliente Demo',
            'nif' => '12345678Z',
            'bank' => null,
            'phone' => '600000000',
            'email' => 'cliente@example.com',
            'address' => 'Dirección cliente',
        ]);

        $this->actingAs($user);

        $firstIssue = Carbon::now()->addDay()->toDateString();

        $response = $this->post(route('invoices.storeWithItems'), [
            'date' => Carbon::now()->toDateString(),
            'state' => 'pending',
            'client_id' => $client->id,
            'invoiceItems' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => 100,
                'discount' => 0,
                'iva' => 21,
            ]],
            'is_recurring' => true,
            'frequency_unit' => 'month',
            'frequency_interval' => 1,
            'first_issue_on' => $firstIssue,
            'generate_first_invoice' => true,
            'recurring_status' => 'active',
            'recurring_invoice_state' => 'pending',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('recurring_invoices', 1);
        $this->assertDatabaseCount('recurring_invoice_items', 1);

        $template = RecurringInvoice::first();
        $this->assertEquals($client->id, $template->client_id);
        $this->assertNotNull($template->next_run_at);
        $this->assertSame('active', $template->status);

        $invoice = Invoice::where('recurring_invoice_id', $template->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('pending', $invoice->state);
        $this->assertEquals(2, $invoice->items()->first()->quantity);
    }

    public function test_console_command_generates_pending_invoices(): void
    {
        $company = Company::create([
            'name' => 'Cron Company',
            'nif' => 'B12345678',
            'phone' => '111222333',
            'email' => 'cron@example.com',
            'address' => 'Cron street',
        ]);

        $client = Client::create([
            'company_id' => $company->id,
            'name' => 'Cliente Cron',
            'nif' => '12312312L',
            'bank' => null,
            'phone' => '600000001',
            'email' => 'cron-client@example.com',
            'address' => 'Dirección cron',
        ]);

        $category = Category::create([
            'company_id' => $company->id,
            'name' => 'Servicios',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'name' => 'Servicio Cron',
            'description' => 'Servicio programado',
            'price' => 50,
            'iva' => 21,
            'stock' => 50,
            'is_stackable' => true,
            'disabled' => false,
        ]);

        $template = RecurringInvoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'status' => 'active',
            'invoice_state' => 'pending',
            'frequency_unit' => 'month',
            'frequency_interval' => 1,
            'first_issue_on' => Carbon::now()->subMonth()->toDateString(),
            'next_run_at' => Carbon::now()->subMinute(),
            'expected_total' => 0,
            'active' => true,
        ]);

        RecurringInvoiceItem::create([
            'recurring_invoice_id' => $template->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 50,
            'discount' => 0,
            'iva' => 21,
        ]);

        $this->artisan('recurring-invoices:generate')
            ->expectsOutputToContain('Encontradas 1 plantillas pendientes de ejecución.')
            ->assertExitCode(0);

        $invoice = Invoice::where('recurring_invoice_id', $template->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(50.0, $invoice->base_imponible);
        $this->assertGreaterThan(Carbon::now(), $template->fresh()->next_run_at);
    }

    public function test_generator_creates_consecutive_numbers_and_items(): void
    {
        $company = Company::create([
            'name' => 'Generator Company',
            'nif' => 'C12345678',
            'phone' => '222333444',
            'email' => 'generator@example.com',
            'address' => 'Generator street',
        ]);

        $client = Client::create([
            'company_id' => $company->id,
            'name' => 'Cliente Generator',
            'nif' => '32132132P',
            'bank' => null,
            'phone' => '600000002',
            'email' => 'generator-client@example.com',
            'address' => 'Dirección generator',
        ]);

        $category = Category::create([
            'company_id' => $company->id,
            'name' => 'Servicios',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'name' => 'Servicio Premium',
            'description' => 'Servicio recurrente premium',
            'price' => 75,
            'iva' => 21,
            'stock' => 20,
            'is_stackable' => true,
            'disabled' => false,
        ]);

        $template = RecurringInvoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'status' => 'active',
            'invoice_state' => 'pending',
            'frequency_unit' => 'month',
            'frequency_interval' => 1,
            'first_issue_on' => Carbon::now()->toDateString(),
            'next_run_at' => Carbon::now()->toDateString(),
            'expected_total' => 0,
            'active' => true,
        ]);

        RecurringInvoiceItem::create([
            'recurring_invoice_id' => $template->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 75,
            'discount' => 0,
            'iva' => 21,
        ]);

        $generator = app(RecurringInvoiceGenerator::class);

        $firstInvoice = $generator->generate($template->fresh(), Carbon::now());
        $this->assertSame('pending', $firstInvoice->state);
        $this->assertEquals(225.0, $firstInvoice->base_imponible);
        $this->assertEquals(1, $firstInvoice->items()->count());

        $template->refresh();
        $template->next_run_at = Carbon::now();
        $template->save();

        $secondInvoice = $generator->generate($template->fresh(), Carbon::now());
        $this->assertNotEquals($firstInvoice->name, $secondInvoice->name);
        $this->assertEquals(225.0, $secondInvoice->base_imponible);
        $this->assertEquals(1, $secondInvoice->items()->count());
    }
}
