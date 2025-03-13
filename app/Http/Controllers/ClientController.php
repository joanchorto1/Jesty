<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('company_id',Auth::user()->company_id);
        return Inertia::render('Clients/Index', [
            'clients' => $clients
        ]);
    }
    public function show(Client $client)
    {
        $invoices = Invoice::where('client_id', $client->id)->get();
        $budgets = Budget::where('client_id', $client->id)->get();
        return Inertia::render('Clients/Show', [
            'client' => $client,
            'invoices' => $invoices,
            'budgets' => $budgets
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create', [
            'company' => Company::where('id',Auth::user()->company_id)->first(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
            'name' => 'required',
            'nif' => 'required',
            'bank' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ]);

        Client::create($request->all());

        app('App\Http\Controllers\UserNotificationController')->createNotification('Nuevo cliente', 'Se ha creado un nuevo cliente', 'Clientes');

        return Inertia::location(route('dashboard.clients'));
    }

    public function edit(Client $client)
    {
        $companies = Company::all();
        return Inertia::render('Clients/Edit', [
            'client' => $client,
            'companies' => $companies
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'company_id' => 'required',
            'name' => 'required',
            'nif' => 'required',
            'bank' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ]);

        $client->update($request->all());

        app('App\Http\Controllers\UserNotificationController')->createNotification('Cliente modificado', 'Se ha modificado un cliente', 'Clientes');

return Inertia::location(route('dashboard.clients'));
    }

    public function destroy(Client $client)
    {

        app('App\Http\Controllers\UserNotificationController')->createNotification('Cliente eliminado', 'Se ha eliminado un cliente', 'Clientes');
        $client->delete();


return Inertia::location(route('dashboard.clients'));   }
}
