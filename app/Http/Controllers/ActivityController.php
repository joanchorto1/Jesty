<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Lead;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('opportunity', 'company')->paginate(10);
        return Inertia::render('Activities/Index', ['activities' => $activities]);
    }

    public function goToCreate(Lead $lead, Opportunity $opportunity)
    {
        return Inertia::location(route('activities.create', ['lead' => $lead, 'opportunity' => $opportunity]));
    }

    public function create(Lead $lead, Opportunity $opportunity)
    {

        return Inertia::render('Activities/Create', ['lead' => $lead, 'opportunity' => $opportunity]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255', // Ejemplo: Llamada, Reunión, etc.
            'notes' => 'nullable|string',
            'date' => 'required|date',
            'opportunity_id' => 'required|exists:opportunities,id',
            'lead_id' => 'required|exists:leads,id',
        ]);

        $request['company_id'] = Opportunity::find($request->opportunity_id)->company_id;


        Activity::create($request->all());
        return Inertia::location(route('opportunities.show', $request->opportunity_id));
    }

    public function show(Activity $activity)
    {
        $activity->load('opportunity', 'company');
        return Inertia::render('Activities/Show', ['activity' => $activity]);
    }

    public function goToEdit(Activity $activity)
    {
        return Inertia::location(route('activities.edit', $activity));
    }

    public function edit(Activity $activity)
    {
        return Inertia::render('Activities/Edit', ['activity' => $activity]);
    }

    public function update(Request $request, Activity $activity)
    {


        $request->validate([
            'type' => 'required|string|max:255', // Ejemplo: Llamada, Reunión, etc.
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $request['opportunity_id'] = $activity->opportunity_id;
        $request['lead_id'] = $activity->lead_id;


        $activity->update($request->all());
        return Inertia::location(route('opportunities.show', $activity->opportunity_id));
    }
    public function destroy(Activity $activity)
    {
        $activity->delete();
        return Inertia::location(route('opportunities.show', $activity->opportunity_id));
    }
}
