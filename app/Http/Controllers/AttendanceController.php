<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index()
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)->get();
        $attendances = Attendance::whereIn('employee_id', $employees->pluck('id'))->get();



        return Inertia::render('Attendances/Index', [
            'attendances' => $attendances,
            'employees' => $employees,
        ]);
    }

    public function create()
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)->get();

        return Inertia::render('Attendances/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'total_hours' => 'required|numeric',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
        ]);

        Attendance::create($data);

        return Inertia::location(route('attendances.index'));
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)->get();

        return Inertia::render('Attendances/Edit', [
            'attendance' => $attendance,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'total_hours' => 'required|numeric',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',

        ]);

        $attendance->update($data);

        return Inertia::location(route('attendances.index'));
    }

    public function show(Attendance $attendance)
    {
        $attendance->load('employee');

        return Inertia::render('Attendances/Show', [
            'attendance' => $attendance,
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return Inertia::location(route('attendances.index'));
    }
}
