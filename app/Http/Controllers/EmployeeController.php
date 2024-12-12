<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\PerformanceReview;
use App\Models\Training;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->with('department')
            ->get();

        return Inertia::render('Employees/Index', [
            'employees' => $employees
        ]);
    }

    public function show($id)
    {
        $employee = Employee::where('company_id', auth()->user()->company_id)
            ->with('department', 'payrolls', 'attendances', 'leaves', 'performanceReviews', 'trainings')
            ->findOrFail($id);

        return Inertia::render('Employees/Show', [
            'employee' => $employee
        ]);
    }

    public function create()
    {
        $departments = Department::where('company_id', auth()->user()->company_id)->get();

        return Inertia::render('Employees/Create', [
            'departments' => $departments
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string',
            'address' => 'nullable|string',
            'job_title' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'salary' => 'required|numeric',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $data['company_id'] = auth()->user()->company_id;

        Employee::create($data);

        return Inertia::location(route('employees.index'));
    }

    public function edit($id)
    {
        $employee = Employee::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $departments = Department::where('company_id', auth()->user()->company_id)->get();

        return Inertia::render('Employees/Edit', [
            'employee' => $employee,
            'departments' => $departments
        ]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::where('company_id', auth()->user()->company_id)->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string',
            'address' => 'nullable|string',
            'job_title' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'salary' => 'required|numeric',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $employee->update($data);

        return Inertia::location(route('employees.index'));
    }
}


