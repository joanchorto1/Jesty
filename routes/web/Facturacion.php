<?php

use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetItemController;
use App\Http\Controllers\CreditNoteController;



Route::middleware(['route.features.access:1'])->group(function() {


    Route::middleware(['auth'])->get('/dashboard/billing', function () {
        return Inertia::render('DashboardFacturacion', [
            'invoices' => Invoice::where('company_id', \Illuminate\Support\Facades\Auth::user()->company_id)->get(),
            'clients' => \App\Models\Client::where('company_id', \Illuminate\Support\Facades\Auth::user()->company_id)->get(),
            'budgets' => \App\Models\Budget::where('company_id', \Illuminate\Support\Facades\Auth::user()->company_id)->get()
        ]);
    })->name('dashboard.billing');


// Routes for Invoices
    Route::resource('invoices', InvoiceController::class)
        ->names([
            'index' => 'invoices.index',
            'create' => 'invoices.create',
            'store' => 'invoices.store',
            'show' => 'invoices.show',
            'edit' => 'invoices.edit',
            'update' => 'invoices.update',
            'destroy' => 'invoices.destroy',
        ]);
    Route::post('/invoices/create-from-budget/{budget}', [InvoiceController::class, 'createFromBudget'])->name('invoices.create-from-budget');
    Route::post('/invoices/store-with-items', [InvoiceController::class, 'storeWithItems'])->name('invoices.storeWithItems');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('/invoices/copy/{invoice}', [InvoiceController::class, 'copy'])->name('invoices.copy');

// Routes for Invoice Items
    Route::resource('invoiceItems', InvoiceItemController::class)
        ->names([
            'index' => 'invoiceItems.index',
            'create' => 'invoiceItems.create',
            'store' => 'invoiceItems.store',
            'show' => 'invoiceItems.show',
            'edit' => 'invoiceItems.edit',
            'update' => 'invoiceItems.update',
            'destroy' => 'invoiceItems.destroy',
        ]);

// Routes for Budgets
    Route::resource('budgets', BudgetController::class)
        ->names([
            'index' => 'budgets.index',
            'create' => 'budgets.create',
            'store' => 'budgets.store',
            'show' => 'budgets.show',
            'edit' => 'budgets.edit',
            'update' => 'budgets.update',
            'destroy' => 'budgets.destroy',
        ]);

    Route::post('/budgets/store-with-items', [BudgetController::class, 'storeWithItems'])->name('budgets.storeWithItems');
    Route::get('/budgets/{budget}/print', [BudgetController::class, 'print'])->name('budgets.print');


    //Envios por email

    Route::get('/budgets/{budget}/send', [BudgetController::class, 'send'])->name('budgets.send');
    Route::get('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');



    // Routes for Credit Notes;

    Route::get('/invoices/{invoice}/credit-notes', [CreditNoteController::class, 'show'])->name('invoices.credit-notes');
    Route::get('/invoices/{invoice}/credit-notes/create', [CreditNoteController::class, 'create'])->name('invoices.credit-notes.create');
    Route::post('/invoices/{invoice}/credit-notes', [CreditNoteController::class, 'store'])->name('invoices.credit-notes.store');
    Route::get('/credit-notes/{creditNote}/edit', [CreditNoteController::class, 'edit'])->name('credit-notes.edit');
    Route::put('/credit-notes/{creditNote}', [CreditNoteController::class, 'update'])->name('credit-notes.update');
    Route::delete('/credit-notes/{creditNote}', [CreditNoteController::class, 'destroy'])->name('credit-notes.destroy');


//// Routes for Budget Items
//    Route::resource('budgetItems', BudgetItemController::class)
//        ->names([
//            'index' => 'budgetItems.index',
//            'create' => 'budgetItems.create',
//            'store' => 'budgetItems.store',
//            'show' => 'budgetItems.show',
//            'edit' => 'budgetItems.edit',
//            'update' => 'budgetItems.update',
//            'destroy' => 'budgetItems.destroy',
//
//        ]);
//    Route::post('budgetItems/storeInLastButget', [BudgetItemController::class, 'storeInLastButget'])->name('budgetItems.storeInLastButget');
//    Route::post('budgetItems/storeMultipleItems', [BudgetItemController::class, 'storeMultipleItems'])->name('budgetItems.storeMultipleItems');


});
