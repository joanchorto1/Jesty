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
    public function report()
    {
        $incomes = Income::where('company_id', Auth::user()->company_id)->get();
        $expenses = Expense::where('company_id', Auth::user()->company_id)->get();
        $clients= Client::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Expenses/Report',[
            'expenses' => $expenses,
            'incomes' => $incomes,
            'clients' => $clients,
        ]);
    }
    public function reportPrint(Request $request)
    {

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $expenses = Expense::where('company_id', auth()->user()->company_id)
            ->when($startDate, function ($query, $startDate) {
                return $query->where('date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('date', '<=', $endDate);
            })
            ->orderBy('date', 'desc')
            ->get();

        $incomes = Income::where('company_id', auth()->user()->company_id)
            ->when($startDate, function ($query, $startDate) {
                return $query->where('date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('date', '<=', $endDate);
            })
            ->orderBy('date', 'desc')
            ->get();

        $clients= Client::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Expenses/ReportPrint', [
            'expenses' => $expenses,
            'incomes' => $incomes,
            'clients' => $clients,
        ]);
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
