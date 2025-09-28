<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $tasks = Task::with(['company', 'taskable'])
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(10);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
        ]);
    }

    public function create(?string $taskableType = null, ?int $taskableId = null)
    {
        $companyId = Auth::user()->company_id;
        $types = $this->allowedTaskableTypes();

        $prefill = null;
        if ($taskableType && isset($types[$taskableType]) && $taskableId) {
            $prefill = [
                'type' => $taskableType,
                'id' => $taskableId,
            ];
        }

        return Inertia::render('Tasks/Create', [
            'leads' => Lead::where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name as label']),
            'opportunities' => Opportunity::where('company_id', $companyId)
                ->orderBy('description')
                ->get(['id', 'description as label']),
            'clients' => Client::where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name as label']),
            'projects' => $this->projectsForCompany($companyId),
            'typeOptions' => $this->taskableTypeOptions(array_keys($types)),
            'statusOptions' => $this->statusOptions(),
            'prefillTaskable' => $prefill,
        ]);
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $types = $this->allowedTaskableTypes();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'required|string|max:255',
            'taskable_type' => ['required', 'string', Rule::in(array_keys($types))],
            'taskable_id' => ['required', 'integer'],
        ]);

        $taskable = $this->resolveTaskable($validated['taskable_type'], (int) $validated['taskable_id'], $types);
        $this->ensureTaskableBelongsToCompany($taskable, $companyId);

        Task::create([
            'title' => $validated['title'],
            'description' => Arr::get($validated, 'description'),
            'due_date' => $validated['due_date'],
            'status' => $validated['status'],
            'company_id' => $companyId,
            'taskable_type' => $types[$validated['taskable_type']],
            'taskable_id' => $taskable->getKey(),
        ]);

        return Redirect::route('tasks.index')->with('success', 'Tarea creada con éxito.');
    }

    public function show(Task $task)
    {
        $task->load(['company', 'taskable']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    public function edit(Task $task)
    {
        $companyId = Auth::user()->company_id;
        $types = $this->allowedTaskableTypes();

        $task->load('taskable');

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'leads' => Lead::where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name as label']),
            'opportunities' => Opportunity::where('company_id', $companyId)
                ->orderBy('description')
                ->get(['id', 'description as label']),
            'clients' => Client::where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name as label']),
            'projects' => $this->projectsForCompany($companyId),
            'typeOptions' => $this->taskableTypeOptions(array_keys($types)),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $companyId = Auth::user()->company_id;
        $types = $this->allowedTaskableTypes();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'required|string|max:255',
            'taskable_type' => ['required', 'string', Rule::in(array_keys($types))],
            'taskable_id' => ['required', 'integer'],
        ]);

        $taskable = $this->resolveTaskable($validated['taskable_type'], (int) $validated['taskable_id'], $types);
        $this->ensureTaskableBelongsToCompany($taskable, $companyId);

        $task->update([
            'title' => $validated['title'],
            'description' => Arr::get($validated, 'description'),
            'due_date' => $validated['due_date'],
            'status' => $validated['status'],
            'company_id' => $companyId,
            'taskable_type' => $types[$validated['taskable_type']],
            'taskable_id' => $taskable->getKey(),
        ]);

        return Redirect::route('tasks.index')->with('success', 'Tarea actualizada con éxito.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return Redirect::route('tasks.index')->with('success', 'Tarea eliminada con éxito.');
    }

    /**
     * @return array<string, class-string<Model>>
     */
    protected function allowedTaskableTypes(): array
    {
        $types = [
            'lead' => Lead::class,
            'opportunity' => Opportunity::class,
            'client' => Client::class,
        ];

        $projectClass = 'App\\Models\\Project';

        if (class_exists($projectClass)) {
            $types['project'] = $projectClass;
        }

        return $types;
    }

    /**
     * @param  array<string, class-string<Model>>  $types
     */
    protected function resolveTaskable(string $type, int $id, array $types): Model
    {
        $class = $types[$type] ?? null;

        abort_if($class === null, 422, 'Tipo de relación inválido.');

        return $class::findOrFail($id);
    }

    protected function ensureTaskableBelongsToCompany(Model $taskable, int $companyId): void
    {
        if (isset($taskable->company_id) && (int) $taskable->company_id !== $companyId) {
            abort(403, 'No puedes asociar tareas con registros de otra compañía.');
        }
    }

    protected function taskableTypeOptions(array $keys): array
    {
        return array_map(function (string $key) {
            return [
                'value' => $key,
                'label' => Str::headline($key),
            ];
        }, $keys);
    }

    protected function statusOptions(): array
    {
        return [
            'pendiente',
            'en progreso',
            'completada',
        ];
    }

    protected function projectsForCompany(int $companyId): array
    {
        $projectClass = 'App\\Models\\Project';

        if (! class_exists($projectClass)) {
            return [];
        }

        $model = new $projectClass();
        $table = $model->getTable();

        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'company_id')) {
            return [];
        }

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $projectClass::query();

        return $query->where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name as label'])
            ->toArray();
    }
}
