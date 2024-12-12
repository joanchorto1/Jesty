<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Employee;
use Inertia\Inertia;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->get();

        return Inertia::render('Payrolls/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function create()
    {
        $employees = Employee::all();

        return Inertia::render('Payrolls/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'base_salary' => 'required|numeric',
            'bonuses' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'net_pay' => 'required|numeric',
            'pay_date' => 'required|date',
            'status' => 'required|string',
        ]);

        Payroll::create($data);

        return Inertia::location(route('payrolls.index'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::all();

        return Inertia::render('Payrolls/Edit', [
            'payroll' => $payroll,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'base_salary' => 'required|numeric',
            'bonuses' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'net_pay' => 'required|numeric',
            'pay_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $payroll->update($data);

        return Inertia::location(route('payrolls.index'));
    }

    public function show(Payroll $payroll)
    {
        return Inertia::render('Payrolls/Show', [
            'payroll' => $payroll
        ]);
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payrolls.index');
    }





}
