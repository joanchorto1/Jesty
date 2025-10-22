<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RecurringExpenseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_template_and_generates_expense_from_command(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $category = ExpenseCategory::factory()->create(['company_id' => $company->id]);
        $paymentMethod = PaymentMethod::factory()->create(['company_id' => $company->id]);

        $nextRun = Carbon::now()->addDay()->toDateString();

        $response = $this->actingAs($user)->post(route('expenses.store'), [
            'name' => 'Suscripción software',
            'description' => 'Licencia mensual',
            'amount' => 100,
            'iva' => 21,
            'date' => Carbon::now()->toDateString(),
            'payment_method_id' => $paymentMethod->id,
            'expense_category_id' => $category->id,
            'recurring' => [
                'enabled' => true,
                'frequency_type' => 'monthly',
                'frequency_interval' => 1,
                'next_run_at' => $nextRun,
                'ends_at' => null,
                'status' => 'active',
            ],
        ]);

        $response->assertStatus(409);
        $response->assertHeader('X-Inertia-Location', route('expenses.index'));

        $template = RecurringExpense::first();
        $expense = Expense::first();

        $this->assertNotNull($template);
        $this->assertNotNull($expense);
        $this->assertSame($template->id, $expense->recurring_expense_id);
        $this->assertSame($company->id, $template->company_id);
        $this->assertEquals($expense->date, $template->last_generated_at?->toDateString());

        Carbon::setTestNow(Carbon::parse($nextRun)->addMinutes(10));

        Artisan::call('expenses:generate-recurring');

        $template->refresh();

        $this->assertDatabaseCount('expenses', 2);
        $generatedExpense = Expense::orderByDesc('id')->first();
        $this->assertEquals($template->id, $generatedExpense->recurring_expense_id);
        $this->assertEquals($nextRun, $generatedExpense->date);
        $this->assertTrue($template->next_run_at->greaterThan(Carbon::parse($nextRun)));
        $this->assertEquals($nextRun, $template->last_generated_at?->toDateString());

        Carbon::setTestNow();
    }
}
