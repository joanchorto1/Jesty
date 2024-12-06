<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Note;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::with('company', 'opportunity')->paginate(10);
        return Inertia::render('Notes/Index', ['notes' => $notes]);
    }

    public function goToCreate(Opportunity $opportunity,Lead $lead)
    {
        return Inertia::location(route('notes.create', ['lead'=>$lead,'opportunity' => $opportunity]));
    }

    public function create(Opportunity $opportunity,Lead $lead)
    {
        return Inertia::render('Notes/Create', ['opportunity' => $opportunity, 'lead'=>$lead]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'lead_id' => 'required|integer', // puede ser Lead, Opportunity, Activity, etc.
            'opportunity_id' => 'required|integer',

        ]);

        $request['company_id'] = Opportunity::find($request->opportunity_id)->company_id;
        Note::create($request->all());
        return Inertia::location(route('opportunities.show', $request->opportunity_id));
    }

    public function show(Note $note)
    {
        return Inertia::render('Notes/Show', ['note' => $note]);
    }

    public function goToEdit(Note $note)
    {
        return Inertia::location(route('notes.edit', $note));
    }

    public function edit(Note $note)
    {
        return Inertia::render('Notes/Edit', ['note' => $note]);
    }

    public function update(Request $request, Note $note)
    {
        $request->validate([
            'content' => 'required|string|max:1000',

        ]);

        $request['opportunity_id'] = $note->opportunity_id;
        $request['lead_id'] = $note->lead_id;

        $note->update($request->all());
        return Inertia::location(route('opportunities.show', $request->opportunity_id));
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return Inertia::location(route('opportunities.show', $note->opportunity_id));
    }
}
