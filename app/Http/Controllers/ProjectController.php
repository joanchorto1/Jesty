<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectResponsible;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with([
            'department',
            'client',
            'phases.responsible',
            'responsibles.employee',
        ])->where('company_id', Auth::user()->company_id)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;

        return Inertia::render('Projects/Create', [
            'departments' => Department::where('company_id', $companyId)->get(),
            'clients' => Client::where('company_id', $companyId)->get(),
            'employees' => Employee::where('company_id', $companyId)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'phases' => 'nullable|array',
            'phases.*.name' => 'required_with:phases|string|max:255',
            'phases.*.status' => 'nullable|string|max:100',
            'phases.*.start_date' => 'nullable|date',
            'phases.*.end_date' => 'nullable|date|after_or_equal:phases.*.start_date',
            'phases.*.responsible_id' => 'nullable|exists:employees,id',
            'phases.*.notes' => 'nullable|string',
            'responsibles' => 'nullable|array',
            'responsibles.*.employee_id' => 'required_with:responsibles|exists:employees,id',
            'responsibles.*.role' => 'nullable|string|max:255',
        ]);

        Department::where('company_id', $companyId)->findOrFail($validated['department_id']);

        if (!empty($validated['client_id'])) {
            Client::where('company_id', $companyId)->findOrFail($validated['client_id']);
        }

        $allowedEmployeeIds = Employee::where('company_id', $companyId)->pluck('id')->all();

        $project = Project::create([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
            'client_id' => $validated['client_id'] ?? null,
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'description' => $validated['description'] ?? null,
            'company_id' => $companyId,
        ]);

        collect($validated['phases'] ?? [])
            ->filter(fn ($phase) => filled($phase['name'] ?? null))
            ->each(function ($phase) use ($project, $allowedEmployeeIds) {
                if (!empty($phase['responsible_id']) && !in_array($phase['responsible_id'], $allowedEmployeeIds, true)) {
                    abort(422, 'El responsable seleccionado no pertenece a la empresa.');
                }

                ProjectPhase::create([
                    'project_id' => $project->id,
                    'name' => $phase['name'],
                    'status' => $phase['status'] ?? 'pending',
                    'start_date' => $phase['start_date'] ?? null,
                    'end_date' => $phase['end_date'] ?? null,
                    'responsible_id' => $phase['responsible_id'] ?? null,
                    'notes' => $phase['notes'] ?? null,
                ]);
            });

        collect($validated['responsibles'] ?? [])
            ->filter(fn ($responsible) => filled($responsible['employee_id'] ?? null))
            ->each(function ($responsible) use ($project, $allowedEmployeeIds) {
                if (!in_array($responsible['employee_id'], $allowedEmployeeIds, true)) {
                    abort(422, 'El responsable seleccionado no pertenece a la empresa.');
                }

                ProjectResponsible::create([
                    'project_id' => $project->id,
                    'employee_id' => $responsible['employee_id'],
                    'role' => $responsible['role'] ?? null,
                ]);
            });

        return Inertia::location(route('projects.show', $project->id));
    }

    public function show(Project $project)
    {
        $project->load(['department', 'client', 'phases.responsible', 'responsibles.employee', 'tasks', 'invoices']);

        $companyId = Auth::user()->company_id;
        abort_unless($project->company_id === $companyId, 403);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'employees' => Employee::where('company_id', $companyId)->get(),
        ]);
    }

    public function edit(Project $project)
    {
        $companyId = Auth::user()->company_id;
        abort_unless($project->company_id === $companyId, 403);

        return Inertia::render('Projects/Edit', [
            'project' => $project->load(['phases', 'responsibles']),
            'departments' => Department::where('company_id', $companyId)->get(),
            'clients' => Client::where('company_id', $companyId)->get(),
            'employees' => Employee::where('company_id', $companyId)->get(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $companyId = Auth::user()->company_id;
        abort_unless($project->company_id === $companyId, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Department::where('company_id', $companyId)->findOrFail($validated['department_id']);

        if (!empty($validated['client_id'])) {
            Client::where('company_id', $companyId)->findOrFail($validated['client_id']);
        }

        $project->update($validated);

        return Inertia::location(route('projects.show', $project->id));
    }

    public function destroy(Project $project)
    {
        $companyId = Auth::user()->company_id;
        abort_unless($project->company_id === $companyId, 403);

        $project->delete();

        return Inertia::location(route('projects.index'));
    }
}
