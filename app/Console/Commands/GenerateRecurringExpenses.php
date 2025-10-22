<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\RecurringExpense;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateRecurringExpenses extends Command
{
    protected $signature = 'expenses:generate-recurring';

    protected $description = 'Genera gastos a partir de plantillas recurrentes programadas.';

    public function handle(): int
    {
        $now = Carbon::now();

        $templates = RecurringExpense::where('status', 'active')
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->where('next_run_at', '<=', $now)
            ->get();

        if ($templates->isEmpty()) {
            $this->info('No hay plantillas pendientes de ejecutar.');
            return self::SUCCESS;
        }

        $templates->each(function (RecurringExpense $template) {
            $generatedExpenseId = DB::transaction(function () use ($template) {
                $scheduledRun = Carbon::parse($template->next_run_at);

                $expense = new Expense([
                    'name' => $template->name,
                    'description' => $template->description,
                    'amount' => $template->amount,
                    'iva' => $template->iva,
                    'date' => $scheduledRun->toDateString(),
                    'payment_method_id' => $template->payment_method_id,
                    'expense_category_id' => $template->expense_category_id,
                    'company_id' => $template->company_id,
                    'recurring_expense_id' => $template->id,
                ]);

                $expense->save();

                $nextRun = $this->calculateNextRun($scheduledRun, $template->frequency_type, $template->frequency_interval);

                $updates = [
                    'last_generated_at' => $scheduledRun,
                    'next_run_at' => $nextRun,
                ];

                if ($template->ends_at && $nextRun && $nextRun->greaterThan(Carbon::parse($template->ends_at))) {
                    $updates['status'] = 'inactive';
                    $updates['next_run_at'] = null;
                }

                $template->update($updates);
                return $expense->id;
            });

            $this->info(sprintf('Generado gasto #%d para la plantilla %d', $generatedExpenseId, $template->id));
        });

        return self::SUCCESS;
    }

    protected function calculateNextRun(Carbon $current, string $type, int $interval): Carbon
    {
        $next = $current->copy();

        return match ($type) {
            'daily' => $next->addDays($interval),
            'weekly' => $next->addWeeks($interval),
            'monthly' => $next->addMonthsNoOverflow($interval),
            'yearly' => $next->addYears($interval),
            default => $next->addMonthsNoOverflow($interval),
        };
    }
}
