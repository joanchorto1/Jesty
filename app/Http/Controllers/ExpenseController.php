<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $user=Auth::user();
        $expenses = Expense::where('company_id', Auth::user()->company_id)->get();
        $paymentMethods = PaymentMethod::where('company_id', Auth::user()->company_id)->get();
        $categories = ExpenseCategory::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'paymentMethods' => $paymentMethods,
            'categories' => $categories,
            'user' => $user,
        ]);
    }

    public function create()
    {
        $categories = ExpenseCategory::all();
        $paymentMethods = PaymentMethod::all();
        return Inertia::render('Expenses/Create', [
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function store(Request $request)
    {


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'iva' => 'required|numeric',
            'date' => 'required|date',
            'payment_method_id' => 'required',
            'expense_category_id' => 'required',
            'file' => 'nullable|file',
        ]);


        $validated['company_id'] = Auth::user()->company_id;

        $expense = new Expense();
        $expense->name = $validated['name'];
        $expense->description = $validated['description'];
        $expense->amount = $validated['amount'];
        $expense->iva = $validated['iva'];
        $expense->date = $validated['date'];
        $expense->payment_method_id = $validated['payment_method_id'];
        $expense->expense_category_id = $validated['expense_category_id'];
        $expense->company_id = $validated['company_id'];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('expenses', 'public');
            $expense->file = $path;
        }

        $expense->save();

        app('App\Http\Controllers\UserNotificationController')->createNotification('Nuevo gasto', 'Se ha creado un nuevo gasto', 'Contabilidad');

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

        //eliminar el archivo al editar
//        $expense->file = null;
//        $expense->save();
        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
        ]);
    }
    public function update(Request $request, Expense $expense)
    {


        try {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'iva' => 'required|numeric',
            'date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'file' => 'nullable|file',
        ]);

        } catch (\Exception $e) {
            return $e;
        }
        if ($request->file) {
            $file = $request->file('file');
            $path = $file->store('expenses', 'public');
            $validated['file'] = $path;
        }

        try {
            $expense->update($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el gasto');
        }


        app('App\Http\Controllers\UserNotificationController')->createNotification('Gasto actualizado', 'Se ha actualizado un gasto', 'Contabilidad');

        return Inertia::location(route('expenses.index'));
    }



    public function destroy(Expense $expense)
    {

        app('App\Http\Controllers\UserNotificationController')->createNotification('Gasto eliminado', 'Se ha eliminado un gasto', 'Contabilidad');

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
            'start_date' => $startDate,
            'end_date' => $endDate,
            'clients' => $clients,
        ]);
    }
}
