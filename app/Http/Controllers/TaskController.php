<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('company_id', Auth::user()->company_id)
            ->with(['lead', 'opportunity', 'project'])
            ->orderByDesc('due_date')
            ->paginate(10);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
        ]);
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;

        return Inertia::render('Tasks/Create', [
            'leads' => Lead::where('company_id', $companyId)->get(),
            'opportunities' => Opportunity::where('company_id', $companyId)->get(),
            'projects' => Project::where('company_id', $companyId)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|string|max:100',
            'lead_id' => 'nullable|exists:leads,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $companyId = Auth::user()->company_id;

        if (!empty($validated['project_id'])) {
            Project::where('company_id', $companyId)->findOrFail($validated['project_id']);
        }

        if (!empty($validated['lead_id'])) {
            Lead::where('company_id', $companyId)->findOrFail($validated['lead_id']);
        }

        if (!empty($validated['opportunity_id'])) {
            Opportunity::where('company_id', $companyId)->findOrFail($validated['opportunity_id']);
        }

        $validated['company_id'] = $companyId;

        Task::create($validated);

        return Inertia::location(route('tasks.index'));
    }

    public function show(Task $task)
    {
        abort_unless($task->company_id === Auth::user()->company_id, 403);

        return Inertia::render('Tasks/Show', [
            'task' => $task->load(['lead', 'opportunity', 'project']),
        ]);
    }

    public function edit(Task $task)
    {
        abort_unless($task->company_id === Auth::user()->company_id, 403);

        $companyId = Auth::user()->company_id;

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'leads' => Lead::where('company_id', $companyId)->get(),
            'opportunities' => Opportunity::where('company_id', $companyId)->get(),
            'projects' => Project::where('company_id', $companyId)->get(),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        abort_unless($task->company_id === Auth::user()->company_id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|string|max:100',
            'lead_id' => 'nullable|exists:leads,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        if (!empty($validated['project_id'])) {
            Project::where('company_id', Auth::user()->company_id)->findOrFail($validated['project_id']);
        }

        if (!empty($validated['lead_id'])) {
            Lead::where('company_id', Auth::user()->company_id)->findOrFail($validated['lead_id']);
        }

        if (!empty($validated['opportunity_id'])) {
            Opportunity::where('company_id', Auth::user()->company_id)->findOrFail($validated['opportunity_id']);
        }

        $task->update($validated);

        return Inertia::location(route('tasks.index'));
    }

    public function destroy(Task $task)
    {
        abort_unless($task->company_id === Auth::user()->company_id, 403);

        $task->delete();
        return Inertia::location(route('tasks.index'));
    }
}
