<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::with('employees')->get();
        $employees= Employee::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Trainings/Index', [
            'trainings' => $trainings,
            'employees'=>$employees,
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
            'employee_ids.*' => 'exists:employees,id', // Verifica que cada ID exista
        ]);

        // Crear la capacitación
        $training = Training::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'date' => $data['date'],
            'duration' => $data['duration'],
            'company_id' => Auth::user()->company_id,
        ]);


        // Asignar empleados a la capacitación
        $training->employees()->attach($data['employee_ids']);

        // Redireccionar a la lista de capacitaciones
        return Inertia::location(route('trainings.index'));
    }

    public function edit(Training $training)
    {
        // Obtener todos los empleados de la empresa actual
        $employees = Employee::where('company_id', Auth::user()->company_id)->get();

        // Obtener los IDs de los empleados asociados a la capacitación
        $trainingEmployeeIds = $training->employees()->pluck('employees.id')->toArray();

        return Inertia::render('Trainings/Edit', [
            'training' => $training,
            'employees' => $employees,
            'trainingEmployeeIds' => $trainingEmployeeIds,
        ]);
    }



    public function update(Request $request, Training $training)
    {
        // Validar los datos
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'duration' => 'required|integer',
            'employee_ids' => 'array',
            'employee_ids.*' => 'exists:employees,id', // Verifica que cada ID exista
        ]);

        // Verificar que el usuario tenga permiso para modificar esta capacitación
        if ($training->company_id !== Auth::user()->company_id) {
            abort(403, 'No tienes permiso para actualizar esta capacitación.');
        }

        // Actualizar la capacitación
        $training->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'date' => $data['date'],
            'duration' => $data['duration'],
        ]);

        // Sincronizar los empleados
        $training->employees()->sync($data['employee_ids']);

        // Redireccionar a la lista de capacitaciones
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
