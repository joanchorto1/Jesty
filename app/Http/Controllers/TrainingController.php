<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::with('employees')->get();

        return Inertia::render('Trainings/Index', [
            'trainings' => $trainings,
        ]);
    }

    public function create()
    {
        $employees = Employee::all();

        return Inertia::render('Trainings/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'duration' => 'required|integer',
            'employee_ids' => 'array',
        ]);

        $training = Training::create($data);
        $training->employees()->sync($data['employee_ids'] ?? []);

        return Inertia::location(route('trainings.index'));
    }

    public function edit(Training $training)
    {
        $employees = Employee::all();

        return Inertia::render('Trainings/Edit', [
            'training' => $training,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Training $training)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'duration' => 'required|integer',
            'employee_ids' => 'array',
        ]);

        $training->update($data);
        $training->employees()->sync($data['employee_ids'] ?? []);

        return Inertia::location(route('trainings.index'));
    }

    public function show(Training $training)
    {
        $training->load('employees');

        return Inertia::render('Trainings/Show', [
            'training' => $training,
        ]);
    }
}
