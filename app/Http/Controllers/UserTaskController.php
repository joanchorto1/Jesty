<?php

namespace App\Http\Controllers;

use App\Models\UserTask;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserTaskController extends Controller
{
    public function index()
    {
        // Mostrar las tareas del usuario autenticado
        $tasks = auth()->user()->userTasks;
        return Inertia::render('User_tasks/Index', compact('tasks'));
    }

    public function create()
    {
        return Inertia::render('User_tasks/create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        auth()->user()->userTasks()->create($request->all());

        return Inertia::location('User_tasks/Index');
    }

    public function edit(UserTask $userTask)
    {
        $this->authorize('update', $userTask);
        return Inertia::render('User_tasks/edit', compact('userTask'));
    }

    public function update(Request $request, UserTask $userTask)
    {
        $this->authorize('update', $userTask);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $userTask->update($request->all());

        return Inertia::location('User_tasks/Index');
    }

    public function destroy(UserTask $userTask)
    {
        $this->authorize('delete', $userTask);
        $userTask->delete();

        return Inertia::location('User_tasks/Index');
    }

    public function markAsCompleted(UserTask $userTask)
    {
        $this->authorize('update', $userTask);
        $userTask->update(['status' => 'completed']);

        return Inertia::location('User_tasks/Index');
    }
}
