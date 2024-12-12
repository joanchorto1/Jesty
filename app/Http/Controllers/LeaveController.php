<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\Employee;
use Inertia\Inertia;


class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('employee')->get();

        return Inertia::render('Leaves/Index', [
            'leaves' => $leaves,
        ]);
    }

    public function create()
    {
        $employees = Employee::all();

        return Inertia::render('Leaves/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'reason' => 'nullable|string',
            'status' => 'required|string',
        ]);

        Leave::create($data);

        return Inertia::location(route('leaves.index'));
    }

    public function edit(Leave $leave)
    {
        $employees = Employee::all();

        return Inertia::render('Leaves/Edit', [
            'leave' => $leave,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Leave $leave)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'reason' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $leave->update($data);

        return Inertia::location(route('leaves.index'));
    }

    public function show(Leave $leave)
    {
        $leave->load('employee');

        return Inertia::render('Leaves/Show', [
            'leave' => $leave,
        ]);
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();

        return redirect()->route('leaves.index');
    }
}
