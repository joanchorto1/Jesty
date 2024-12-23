<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\PerformanceReview;
use App\Models\Training;


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
        $employee= Employee::findOrFail($id);
        $department= Department::findOrFail($employee->department_id);
        $payrolls = Payroll::where('employee_id', $id)->get();
        $attendances = Attendance::where('employee_id', $id)->get();
        $leaves = Leave::where('employee_id', $id)->get();
        $performanceReviews = PerformanceReview::where('employee_id', $id)->get();
        $trainings = Training::where('company_id', Auth::user()->company_id)->get();
        $trainings = $trainings->filter(function ($training) use ($employee) {
            return $training->employees->contains($employee);
        });



        return Inertia::render('Employees/Show', [
            'employee' => $employee,
            'department' => $department,
            'payrolls' => $payrolls,
            'attendances' => $attendances,
            'leaves' => $leaves,
            'performanceReviews' => $performanceReviews,
            'trainings' => $trainings

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
            'department_id' => 'nullable',
            'salary' => 'required|numeric',
            'hire_date' => 'required|date',
            'status' => 'required',
            'dni' => 'required|string|max:20|unique:employees,dni',
            'nnss' => 'required|string|max:20|unique:employees,nnss',
            'iban' => 'required|string|max:34|unique:employees,iban',
            'birth_date' => 'required|date',
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
            'status' => 'required',
            'dni' => 'required|string|max:20|unique:employees,dni,' . $employee->id,
            'nnss' => 'required|string|max:20|unique:employees,nnss,' . $employee->id,
            'iban' => 'required|string|max:34|unique:employees,iban,' . $employee->id,
            'birth_date' => 'required|date',
        ]);

        $employee->update($data);

        return Inertia::location(route('employees.index'));
    }
}


