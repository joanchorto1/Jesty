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
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('projects')
            ->where('company_id', Auth::user()->company_id)
            ->get();
        $employees = Employee::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Departments/Index', [
            'departments' => $departments,
            'employees' => $employees
        ]);
    }

    public function show($id)
    {
        $department = Department::where('company_id', auth()->user()->company_id)
            ->with(['manager', 'employees', 'projects.phases', 'projects.responsibles.employee'])
            ->findOrFail($id);

        return Inertia::render('Departments/Show', [
            'department' => $department
        ]);
    }

    public function create()
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)->get();

        return Inertia::render('Departments/Create', [
            'employees' => $employees
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:employees,id',
        ]);

        $data['company_id'] = auth()->user()->company_id;

        Department::create($data);

        return Inertia::location(route('departments.index'));
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $employees = Employee::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Departments/Edit', [
            'department' => $department,
            'employees' => $employees
        ]);
    }

    public function update(Request $request, $id)
    {
        $department = Department::where('company_id', auth()->user()->company_id)->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:employees,id',
        ]);

        $department->update($data);

        return Inertia::location(route('departments.index'));
    }
}
