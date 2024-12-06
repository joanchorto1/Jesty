<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OpportunityController extends Controller
{
    public function index()
    {
        $company= Company::where('id',Auth::user()->company_id)->first();
        $opportunities = Opportunity::where('company_id', $company->id)->get();
        $leads = Lead::where('company_id', $company->id)->get();
        return Inertia::render('Opportunities/Index', ['opportunities' => $opportunities, 'leads' => $leads]);
    }

    public function create()
    {
        return Inertia::render('Opportunities/Create', ['leads' => Lead::where('company_id', Auth::user()->company_id)->get()]);
    }
    public function goToCreateWL(Lead $lead)
    {
        return Inertia::location(route('opportunities.createWithLead', $lead));
    }


    public function createWithLead(Lead $lead)
    {
        return Inertia::render('Opportunities/CreateWithLead', [ 'lead' => $lead]);

    }


    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string',
            'value' => 'required|numeric',
            'status' => 'required|string|max:255',
            'probability' => 'required|numeric',
            'lead_id' => 'required|exists:leads,id',
        ]);

        $company_id = Company::where('id', Auth::user()->company_id)->first();
        $opportunity = $request->all();
        $opportunity['company_id'] = $company_id->id;
        Opportunity::create($opportunity);
        return Inertia::location(route('opportunities.index'));
    }

    public function show(Opportunity $opportunity)
    {
        $activities = Activity::where('opportunity_id', $opportunity->id)->get();
        $notes = Note::where('opportunity_id', $opportunity->id)->get();
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Opportunities/Show', ['opportunity' => $opportunity, 'activities' => $activities, 'notes' => $notes, 'leads' => $leads]);
    }

    public function goToEdit(Opportunity $opportunity)
    {
        return Inertia::location(route('opportunities.edit', $opportunity));
    }

    public function edit(Opportunity $opportunity)
    {
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Opportunities/Edit', ['opportunity' => $opportunity, 'leads' => $leads]);
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        $request->validate([
            'description' => 'nullable|string',
            'value' => 'required|numeric',
            'status' => 'required|string|max:255',
            'lead_id' => 'required|exists:leads,id',
            'probability' => 'required|numeric',
            ]);

        $opportunity->update($request->all());
        return Inertia::location(route('opportunities.show', $opportunity->id));
    }

    public function destroy(Opportunity $opportunity)
    {
        $opportunity->delete();
return Inertia::location(route('opportunities.index'));
    }
}
