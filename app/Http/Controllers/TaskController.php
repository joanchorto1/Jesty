<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('company', 'opportunity')->paginate(10);
        return Inertia::render('Tasks/Index', ['tasks' => $tasks]);
    }

    public function create()
    {
        return Inertia::render('Tasks/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'required|string', // Puede ser 'pendiente', 'en progreso', 'completada', etc.
            'taskable_type' => 'required|string', // puede ser Lead, Opportunity, Activity, etc.
            'taskable_id' => 'required|integer',
            'company_id' => 'required|exists:companies,id',
        ]);

        Task::create($request->all());
        return redirect()->route('tasks.index')->with('success', 'Tarea creada con éxito.');
    }

    public function show(Task $task)
    {
        return Inertia::render('Tasks/Show', ['task' => $task]);
    }

    public function edit(Task $task)
    {
        return Inertia::render('Tasks/Edit', ['task' => $task]);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'required|string',
            'taskable_type' => 'required|string',
            'taskable_id' => 'required|integer',
            'company_id' => 'required|exists:companies,id',
        ]);

        $task->update($request->all());
        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada con éxito.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada con éxito.');
    }
}
