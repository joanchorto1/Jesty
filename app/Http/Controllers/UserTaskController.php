<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserTaskController extends Controller
{


    public function create()
    {
        return Inertia::render('User_tasks/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

            UserTask::create([
               'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'status' => 'pending',
                'user_id'=> Auth::user()->id,
            ]);

        return Inertia::location('/dashboard');
    }

    public function edit(UserTask $userTask)
    {

        return Inertia::render('User_tasks/Edit', [
            'task' => $userTask
        ]);
    }

    public function update(Request $request, UserTask $userTask)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $userTask->update($request->all());

        return Inertia::location('/dashboard');
    }

    public function destroy(UserTask $userTask)
    {
        $userTask->delete();

        return Inertia::location('/dashboard');
    }

    public function markAsCompleted(UserTask $userTask)
    {
        $userTask->update(['status' => 'completed']);

        return Inertia::location('/dashboard');
    }

    public function markAsInProgress(UserTask $userTask)
    {

        $userTask->update(['status' => 'in_progress']);

        return Inertia::location('/dashboard');

    }
    public function show(UserTask $userTask)
    {

        return Inertia::render('User_tasks/Show', [
            'task' => $userTask
        ]);

    }
}
