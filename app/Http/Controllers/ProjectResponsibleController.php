<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectResponsible;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectResponsibleController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless($project->company_id === Auth::user()->company_id, 403);

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'nullable|string|max:255',
        ]);

        $this->assertEmployeeBelongsToCompany($data['employee_id']);

        $project->responsibles()->create($data);

        return Inertia::location(route('projects.show', $project->id));
    }

    public function update(Request $request, Project $project, ProjectResponsible $responsible)
    {
        abort_unless($project->company_id === Auth::user()->company_id, 403);
        abort_unless($responsible->project_id === $project->id, 404);

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'nullable|string|max:255',
        ]);

        $this->assertEmployeeBelongsToCompany($data['employee_id']);

        $responsible->update($data);

        return Inertia::location(route('projects.show', $project->id));
    }

    public function destroy(Project $project, ProjectResponsible $responsible)
    {
        abort_unless($project->company_id === Auth::user()->company_id, 403);
        abort_unless($responsible->project_id === $project->id, 404);

        $responsible->delete();

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
