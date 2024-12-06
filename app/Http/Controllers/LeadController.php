<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index()
    {

        $leads = Lead::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Leads/Index', ['leads' => $leads]);
    }

    public function create()
    {
        return Inertia::render('Leads/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        $company = Company::where('id',Auth::user()->company_id)->first();
        $lead = $request->all();
        $lead['company_id'] = $company->id;




        Lead::create($lead);


return Inertia::location(route('leads.index'));
    }

    public function show(Lead $lead)
    {
        $opportunities = Opportunity::where('lead_id', $lead->id)->get();
        return Inertia::render('Leads/Show', ['lead' => $lead, 'opportunities' => $opportunities]);
    }

    public function edit(Lead $lead)
    {
        return Inertia::render('Leads/Edit', ['lead' => $lead]);
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        $lead->update($request->all());
        return Inertia::location(route('leads.index'));
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return Inertia::location(route('leads.index'));
    }

    public function convertToClient(Lead $lead)
    {
        $client= [];
        $client['name'] = $lead->name;
        $client['phone'] = $lead->phone;
        $client['email'] = $lead->email;
        $client['address'] = '.';
        $client['nif']= '.';
        $client['bank']= '.';
        $client['company_id'] = $lead->company_id;

        $client=Client::create($client);
        $lead->delete();
        return Inertia::location(route('clients.show', $client->id));
    }
}
