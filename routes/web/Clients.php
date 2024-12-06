<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['route.features.access:6'])->group(function(){


Route::get('/dashboard/clients', function () {
    return Inertia::render('DashboardClientes', ['clients' => \App\Models\Client::where('company_id', \Illuminate\Support\Facades\Auth::user()->company_id)->get()]);
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
