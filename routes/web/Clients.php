<?php

use App\Http\Controllers\ClientController;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['route.features.access:6'])->group(function(){


Route::get('/dashboard/clients', function () {
    $companyId = Auth::user()->company_id;

    return Inertia::render('DashboardClientes', [
        'clients' => Client::where('company_id', $companyId)->get(),
        'invoices' => Invoice::where('company_id', $companyId)->get(),
    ]);
})->name('dashboard.clients');


// Routes for Clients
Route::resource('clients', ClientController::class)
    ->names([
        'index' => 'clients.index',
        'create' => 'clients.create',
        'store' => 'clients.store',
        'show' => 'clients.show',
        'edit' => 'clients.edit',
        'update' => 'clients.update',
        'destroy' => 'clients.destroy',
    ]);

});
