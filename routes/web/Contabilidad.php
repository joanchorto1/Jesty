<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['route.features.access:2'])->group(function(){


Route::get('/dashboard/accounting', function () {
    $company = \App\Models\Company::where('id', \Illuminate\Support\Facades\Auth::user()->company_id)->first();
    return Inertia::render('DashboardContabilidad',
        ['clients' => \App\Models\Client::where('company_id', $company->id)->get(),
            'incomes' => \App\Models\Income::where('company_id', $company->id)->get(),
            'expenses' => \App\Models\Expense::where('company_id', $company->id)->get(),
            'ExpensesCategories' => \App\Models\ExpenseCategory::where('company_id', $company->id)->get(),
            'paymentMethods' => \App\Models\PaymentMethod::where('company_id', $company->id)->get()
        ]);
})->name('dashboard.accounting');

Route::resource('expenses', ExpenseController::class);
Route::post('/expenses/{expense}/update', [ExpenseController::class, 'update'])->name('expenses.update2');
//Rote for expenses report
Route::get('report', [ExpenseController::class, 'report'])->name('expenses.report');
//Rote for expenses reportPrint
Route::get('reportPrint', [ExpenseController::class, 'reportPrint'])->name('expenses.reportsPrint');


Route::resource('expenseCategories', ExpenseCategoryController::class);
Route::resource('paymentMethods', PaymentMethodController::class);




Route::middleware(['auth'])->group(function () {
    // Listado de ingresos
    Route::get('/incomes', [IncomeController::class, 'index'])->name('incomes.index');

    // Crear ingreso
    Route::get('/incomes/create', [IncomeController::class, 'create'])->name('incomes.create');
    Route::post('/incomes', [IncomeController::class, 'store'])->name('incomes.store');

    // Ver ingreso
    Route::get('/incomes/{income}', [IncomeController::class, 'show'])->name('incomes.show');

    // Editar ingreso
    Route::get('/incomes/{income}/edit', [IncomeController::class, 'edit'])->name('incomes.edit');
    Route::put('/incomes/{income}', [IncomeController::class, 'update'])->name('incomes.update');

    // Eliminar ingreso
    Route::delete('/incomes/{income}', [IncomeController::class, 'destroy'])->name('incomes.destroy');
});


});
