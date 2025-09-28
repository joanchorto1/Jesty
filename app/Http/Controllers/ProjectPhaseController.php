<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectPhaseController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless($project->company_id === Auth::user()->company_id, 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'responsible_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['responsible_id'])) {
            $this->assertEmployeeBelongsToCompany($data['responsible_id']);
        }

        $project->phases()->create($data);

        return Inertia::location(route('projects.show', $project->id));
    }

    public function update(Request $request, Project $project, ProjectPhase $phase)
    {
        abort_unless($project->company_id === Auth::user()->company_id, 403);
        abort_unless($phase->project_id === $project->id, 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'responsible_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['responsible_id'])) {
            $this->assertEmployeeBelongsToCompany($data['responsible_id']);
        }

        $phase->update($data);

        return Inertia::location(route('projects.show', $project->id));
    }

    public function destroy(Project $project, ProjectPhase $phase)
    {
        abort_unless($project->company_id === Auth::user()->company_id, 403);
        abort_unless($phase->project_id === $project->id, 404);

        $phase->delete();

        return Inertia::location(route('projects.show', $project->id));
    }

    protected function assertEmployeeBelongsToCompany(int $employeeId): void
    {
        $companyEmployeeIds = Employee::where('company_id', Auth::user()->company_id)->pluck('id');

        if (!$companyEmployeeIds->contains($employeeId)) {
            abort(422, 'El responsable seleccionado no pertenece a la empresa.');
        }
    }
}
