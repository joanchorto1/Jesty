<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Client;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\PaymentMethod;
use App\Models\RecurringExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $expenses = Expense::with('recurringTemplate')
            ->where('company_id', $user->company_id)
            ->get()
            ->map(function (Expense $expense) {
                $template = $expense->recurringTemplate;

                return array_merge($expense->toArray(), [
                    'is_recurring' => $template !== null,
                    'recurring_status' => $template?->status,
                    'next_run_at' => optional($template?->next_run_at)?->toDateTimeString(),
                ]);
            })
            ->values();
        $paymentMethods = PaymentMethod::where('company_id', $user->company_id)->get();
        $categories = ExpenseCategory::where('company_id', $user->company_id)->get();

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'paymentMethods' => $paymentMethods,
            'categories' => $categories,
            'user' => $user,
            'frequencyOptions' => $this->frequencyOptions(),
        ]);
    }

    public function create()
    {
        $categories = ExpenseCategory::all();
        $paymentMethods = PaymentMethod::all();

        return Inertia::render('Expenses/Create', [
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
            'frequencyOptions' => $this->frequencyOptions(),
        ]);
    }

    public function store(ExpenseRequest $request)
    {
        $validated = $request->validated();

        $expense = new Expense();
        $expense->fill(collect($validated)->only([
            'name',
            'description',
            'amount',
            'iva',
            'date',
            'payment_method_id',
            'expense_category_id',
        ])->toArray());
        $expense->company_id = Auth::user()->company_id;

        if ($request->hasFile('file')) {
            $expense->file = $request->file('file')->store('expenses', 'public');
        }

        $expense->save();

        $this->syncRecurringTemplate($expense, $validated, true);

        app('App\\Http\\Controllers\\UserNotificationController')->createNotification('Nuevo gasto', 'Se ha creado un nuevo gasto', 'Contabilidad');

        return Inertia::location(route('expenses.index'));
    }

    public function show(Expense $expense)
    {
        $categories = ExpenseCategory::where('company_id', Auth::user()->company_id)->get();
        $paymentMethods = PaymentMethod::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Expenses/Show', [
            'expense' => $expense,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,

        ]);
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();
        $paymentMethods = PaymentMethod::all();

        $expense->load('recurringTemplate');

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
            'frequencyOptions' => $this->frequencyOptions(),
            'recurring' => $expense->recurringTemplate ? [
                'id' => $expense->recurringTemplate->id,
                'enabled' => $expense->recurringTemplate->status !== 'inactive',
                'frequency_type' => $expense->recurringTemplate->frequency_type,
                'frequency_interval' => $expense->recurringTemplate->frequency_interval,
                'next_run_at' => optional($expense->recurringTemplate->next_run_at)?->toDateString(),
                'ends_at' => optional($expense->recurringTemplate->ends_at)?->toDateString(),
                'status' => $expense->recurringTemplate->status,
            ] : [
                'enabled' => false,
            ],
        ]);
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            if ($expense->file) {
                Storage::disk('public')->delete($expense->file);
            }
            $validated['file'] = $request->file('file')->store('expenses', 'public');
        }

        $expense->update(collect($validated)->only([
            'name',
            'description',
            'amount',
            'iva',
            'date',
            'payment_method_id',
            'expense_category_id',
            'file',
        ])->toArray());

        $this->syncRecurringTemplate($expense, $validated);

        app('App\\Http\\Controllers\\UserNotificationController')->createNotification('Gasto actualizado', 'Se ha actualizado un gasto', 'Contabilidad');

        return Inertia::location(route('expenses.index'));
    }

    public function destroy(Expense $expense)
    {

        app('App\\Http\\Controllers\\UserNotificationController')->createNotification('Gasto eliminado', 'Se ha eliminado un gasto', 'Contabilidad');

        $expense->delete();

        return Inertia::location(route('expenses.index'));
    }
    //Reports
    public function report(Request $request)
    {
        $mode = $this->resolveMode($request->string('mode')->toString());
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $companyId = Auth::user()->company_id;

        $expenses = Expense::where('company_id', $companyId)
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->orderBy('date')
            ->get();

        $incomes = Income::where('company_id', $companyId)
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->orderBy('date')
            ->get();

        $clients= Client::where('company_id', $companyId)->get();

        return Inertia::render('Expenses/Report',[
            'report' => $this->buildReportPayload($expenses, $incomes, $mode),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'mode' => $mode,
            ],
            'clients' => $clients,
        ]);
    }

    public function reportPrint(Request $request)
    {
        $mode = $this->resolveMode($request->string('mode')->toString());
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $companyId = Auth::user()->company_id;

        $expenses = Expense::where('company_id', $companyId)
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->orderBy('date')
            ->get();

        $incomes = Income::where('company_id', $companyId)
            ->when($startDate, function ($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->orderBy('date')
            ->get();

        $clients= Client::where('company_id', $companyId)->get();

        return Inertia::render('Expenses/ReportPrint', [
            'report' => $this->buildReportPayload($expenses, $incomes, $mode),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'mode' => $mode,
            ],
            'clients' => $clients,
        ]);
    }

    private function resolveMode(?string $mode): string
    {
        $mode = $mode ?: 'general';

        return in_array($mode, ['monthly', 'annual', 'general'], true) ? $mode : 'general';
    }

    private function buildReportPayload($expenses, $incomes, string $mode): array
    {
        $mode = $this->resolveMode($mode);

        $groups = $this->buildPeriodGroups($expenses, $incomes, $mode);

        $overallSummary = $this->buildSummary($expenses, $incomes);

        return [
            'mode' => $mode,
            'groups' => $groups,
            'overall' => [
                'label' => 'Resumen general',
                'summary' => $overallSummary,
            ],
        ];
    }

    private function buildPeriodGroups($expenses, $incomes, string $mode): array
    {
        if ($mode === 'general') {
            return [
                $this->buildPeriod('general', 'Resumen general', $expenses, $incomes),
            ];
        }

        $expenseGroups = $expenses->groupBy(function ($expense) use ($mode) {
            if (empty($expense->date)) {
                return 'undefined';
            }

            $date = Carbon::parse($expense->date);

            return $mode === 'monthly' ? $date->format('Y-m') : $date->format('Y');
        });

        $incomeGroups = $incomes->groupBy(function ($income) use ($mode) {
            if (empty($income->date)) {
                return 'undefined';
            }

            $date = Carbon::parse($income->date);

            return $mode === 'monthly' ? $date->format('Y-m') : $date->format('Y');
        });

        $keys = $expenseGroups->keys()->merge($incomeGroups->keys())->unique()->sort();

        return $keys->map(function (string $key) use ($expenseGroups, $incomeGroups, $mode) {
            $label = $this->formatPeriodLabel($key, $mode);
            $periodExpenses = $expenseGroups->get($key, collect());
            $periodIncomes = $incomeGroups->get($key, collect());

            return $this->buildPeriod($key, $label, $periodExpenses, $periodIncomes, $mode);
        })->values()->all();
    }

    private function buildPeriod(string $key, string $label, $expenses, $incomes, string $mode = 'general'): array
    {
        $expenseTotals = $this->calculateExpenseTotals($expenses);
        $incomeTotals = $this->calculateIncomeTotals($incomes);
        $summary = $this->calculateSummary($expenseTotals, $incomeTotals);

        return [
            'key' => $key,
            'label' => $label,
            'mode' => $mode,
            'range' => $this->determineRange($key, $mode, $expenses, $incomes),
            'expenses' => [
                'items' => $expenses->values(),
                'totals' => $expenseTotals,
            ],
            'incomes' => [
                'items' => $incomes->values(),
                'totals' => $incomeTotals,
            ],
            'summary' => $summary,
        ];
    }

    private function determineRange(string $key, string $mode, $expenses, $incomes): array
    {
        if ($mode === 'general') {
            $dates = $expenses->pluck('date')->merge($incomes->pluck('date'))->filter();
            $start = $dates->min();
            $end = $dates->max();

            return [
                'start' => $start,
                'end' => $end,
            ];
        }

        if ($key === 'undefined') {
            return [
                'start' => null,
                'end' => null,
            ];
        }

        if ($mode === 'monthly') {
            $date = Carbon::createFromFormat('Y-m', $key)->startOfMonth();

            return [
                'start' => $date->toDateString(),
                'end' => $date->endOfMonth()->toDateString(),
            ];
        }

        $date = Carbon::createFromFormat('Y', $key)->startOfYear();

        return [
            'start' => $date->toDateString(),
            'end' => $date->endOfYear()->toDateString(),
        ];
    }

    private function formatPeriodLabel(string $key, string $mode): string
    {
        if ($key === 'undefined') {
            return 'Sin fecha';
        }

        if ($mode === 'monthly') {
            return Carbon::createFromFormat('Y-m', $key)->translatedFormat('F Y');
        }

        if ($mode === 'annual') {
            return Carbon::createFromFormat('Y', $key)->format('Y');
        }

        return 'Resumen general';
    }

    private function calculateExpenseTotals($expenses): array
    {
        $base = $expenses->sum(function ($expense) {
            return (float) ($expense->amount ?? 0);
        });

        $tax = $expenses->sum(function ($expense) {
            $amount = (float) ($expense->amount ?? 0);
            $rate = (float) ($expense->iva ?? 0);

            return $amount * $rate / 100;
        });

        return [
            'count' => $expenses->count(),
            'base' => $base,
            'tax' => $tax,
            'total' => $base + $tax,
        ];
    }

    private function calculateIncomeTotals($incomes): array
    {
        $base = $incomes->sum(function ($income) {
            return (float) ($income->tax_base ?? 0);
        });

        $tax = $incomes->sum(function ($income) {
            $taxAmount = $income->tax_amount;

            if ($taxAmount !== null) {
                return (float) $taxAmount;
            }

            $base = (float) ($income->tax_base ?? 0);
            $rate = (float) ($income->tax_rate ?? 0);

            return $base * $rate / 100;
        });

        $total = $incomes->sum(function ($income) {
            if ($income->total_amount !== null) {
                return (float) $income->total_amount;
            }

            $base = (float) ($income->tax_base ?? 0);
            $taxAmount = (float) ($income->tax_amount ?? 0);

            return $base + $taxAmount;
        });

        return [
            'count' => $incomes->count(),
            'base' => $base,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    private function calculateSummary(array $expenseTotals, array $incomeTotals): array
    {
        $grossMargin = $incomeTotals['base'] - $expenseTotals['base'];
        $ivaBalance = $incomeTotals['tax'] - $expenseTotals['tax'];
        $net = $incomeTotals['total'] - $expenseTotals['total'];

        return [
            'total_income' => $incomeTotals['total'],
            'total_income_base' => $incomeTotals['base'],
            'total_income_tax' => $incomeTotals['tax'],
            'total_expense_base' => $expenseTotals['base'],
            'total_expense_tax' => $expenseTotals['tax'],
            'total_expense_total' => $expenseTotals['total'],
            'gross_margin' => $grossMargin,
            'iva_balance' => $ivaBalance,
            'net_balance' => $net,
        ];
    }

    private function buildSummary($expenses, $incomes): array
    {
        $expenseTotals = $this->calculateExpenseTotals($expenses);
        $incomeTotals = $this->calculateIncomeTotals($incomes);

        return $this->calculateSummary($expenseTotals, $incomeTotals);
    }

    protected function syncRecurringTemplate(Expense $expense, array $validated, bool $isNew = false): void
    {
        $recurring = $validated['recurring'] ?? null;

        if (!is_array($recurring)) {
            return;
        }

        $enabled = $recurring['enabled'] ?? false;
        $template = $expense->recurringTemplate;

        if (!$enabled) {
            if ($template) {
                $nextRun = $recurring['next_run_at'] ?? null;
                $endsAt = $recurring['ends_at'] ?? null;

                $template->update([
                    'status' => $recurring['status'] ?? 'inactive',
                    'next_run_at' => $nextRun ? Carbon::parse($nextRun) : $template->next_run_at,
                    'ends_at' => $endsAt ? Carbon::parse($endsAt) : $template->ends_at,
                ]);
            }

            return;
        }

        $data = [
            'company_id' => $expense->company_id,
            'expense_category_id' => $validated['expense_category_id'],
            'payment_method_id' => $validated['payment_method_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'iva' => $validated['iva'],
            'frequency_type' => $recurring['frequency_type'],
            'frequency_interval' => $recurring['frequency_interval'],
            'next_run_at' => Carbon::parse($recurring['next_run_at']),
            'ends_at' => $recurring['ends_at'] ? Carbon::parse($recurring['ends_at']) : null,
            'status' => $recurring['status'] ?? 'active',
        ];

        if ($template) {
            $template->update($data);
        } else {
            $template = RecurringExpense::create(array_merge($data, [
                'last_generated_at' => Carbon::parse($validated['date']),
            ]));
            $template->expenses()->save($expense);
        }

        $lastGenerated = Carbon::parse($validated['date']);

        if ($isNew || optional($template->last_generated_at)?->toDateString() !== $lastGenerated->toDateString()) {
            $template->update([
                'last_generated_at' => $lastGenerated,
            ]);
        }

        if ($expense->recurring_expense_id !== $template->id) {
            $expense->update(['recurring_expense_id' => $template->id]);
        }
    }

    private function frequencyOptions(): array
    {
        return [
            ['value' => 'daily', 'label' => 'Diario'],
            ['value' => 'weekly', 'label' => 'Semanal'],
            ['value' => 'monthly', 'label' => 'Mensual'],
            ['value' => 'yearly', 'label' => 'Anual'],
        ];
    }
}
