<?php

namespace App\Http\Controllers;

use App\Models\EmailConfiguration;
use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf; // Importa DomPDF
use Illuminate\Support\Facades\Mail;
use App\Mail\PayrollEmail;



class PayrollController extends Controller
{
    public function index()
    {
        // Obtener los empleados de la empresa del usuario autenticado
        $employees = Employee::where('company_id', Auth::user()->company_id)->get();

        // Encontrar las nóminas relacionadas con los empleados de la empresa
        $payrolls = Payroll::whereIn('employee_id', $employees->pluck('id'))->get();

        return Inertia::render('Payrolls/Index', [
            'payrolls' => $payrolls,
            'employees' => $employees,
        ]);
    }

    public function create()
    {
        // Obtener los empleados de la empresa
        $employees = Employee::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Payrolls/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_date' => 'required|date',
            'base_salary' => 'required|numeric',
            'overtime' => 'nullable|numeric',
            'bonuses' => 'nullable|numeric',
            'commissions' => 'nullable|numeric',
            'vacation_bonus' => 'nullable|numeric',
            'annual_bonus' => 'nullable|numeric',
            'other_earnings' => 'nullable|numeric',
            'income_tax' => 'nullable|numeric',
            'social_security' => 'nullable|numeric',
            'housing_fund' => 'nullable|numeric',
            'loans' => 'nullable|numeric',
            'savings_fund' => 'nullable|numeric',
            'union_dues' => 'nullable|numeric',
            'other_deductions' => 'nullable|numeric',
            'irpf_percentage' => 'nullable|numeric',
            'irpf_deduction' => 'nullable|numeric',

        ]);

        // Calcular days_worked
        $startDate = \Carbon\Carbon::parse($data['start_date']);
        $endDate = \Carbon\Carbon::parse($data['end_date']);
        $daysWorked = $endDate->diffInDays($startDate) + 1; // Sumar 1 para incluir el primer día

        // Calcular total_earnings
        $totalEarnings = $data['base_salary'] + ($data['overtime'] ?? 0) + ($data['bonuses'] ?? 0) +
            ($data['commissions'] ?? 0) + ($data['vacation_bonus'] ?? 0) +
            ($data['annual_bonus'] ?? 0) + ($data['other_earnings'] ?? 0);

        // Calcular total_deductions
        $totalDeductions = ($data['income_tax'] ?? 0) + ($data['social_security'] ?? 0) +
            ($data['housing_fund'] ?? 0) + ($data['loans'] ?? 0) +
            ($data['savings_fund'] ?? 0) + ($data['union_dues'] ?? 0) +
            ($data['other_deductions'] ?? 0);

        // Calcular net_pay
        $irpfAmount = ($totalEarnings * ($data['irpf_percentage'] ?? 0)) / 100;
        $netPay = $totalEarnings - $totalDeductions - $irpfAmount;

        // Ahora agregar estos cálculos a los datos de la nómina
        $data['days_worked'] = $daysWorked;
        $data['total_earnings'] = $totalEarnings;
        $data['total_deductions'] = $totalDeductions;
        $data['net_pay'] = $netPay;

        // Procesar y guardar la nómina
        Payroll::create($data);

        return Inertia::location(route('payrolls.index'));
    }


    public function edit(Payroll $payroll)
    {
        $employees = Employee::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Payrolls/Edit', [
            'payroll' => $payroll,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Payroll $payroll)
    {

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_date' => 'required|date',
            'base_salary' => 'required|numeric',
            'overtime' => 'nullable|numeric',
            'bonuses' => 'nullable|numeric',
            'commissions' => 'nullable|numeric',
            'vacation_bonus' => 'nullable|numeric',
            'annual_bonus' => 'nullable|numeric',
            'other_earnings' => 'nullable|numeric',
            'income_tax' => 'nullable|numeric',
            'social_security' => 'nullable|numeric',
            'housing_fund' => 'nullable|numeric',
            'loans' => 'nullable|numeric',
            'savings_fund' => 'nullable|numeric',
            'union_dues' => 'nullable|numeric',
            'other_deductions' => 'nullable|numeric',
            'irpf_percentage' => 'nullable|numeric',
            'irpf_deduction' => 'nullable|numeric',

        ]);

        // Calcular days_worked
        $startDate = \Carbon\Carbon::parse($data['start_date']);
        $endDate = \Carbon\Carbon::parse($data['end_date']);
        $daysWorked = $endDate->diffInDays($startDate) + 1; // Sumar 1 para incluir el primer día

        // Calcular total_earnings
        $totalEarnings = $data['base_salary'] + ($data['overtime'] ?? 0) + ($data['bonuses'] ?? 0) +
            ($data['commissions'] ?? 0) + ($data['vacation_bonus'] ?? 0) +
            ($data['annual_bonus'] ?? 0) + ($data['other_earnings'] ?? 0);

        // Calcular total_deductions
        $totalDeductions = ($data['income_tax'] ?? 0) + ($data['social_security'] ?? 0) +
            ($data['housing_fund'] ?? 0) + ($data['loans'] ?? 0) +
            ($data['savings_fund'] ?? 0) + ($data['union_dues'] ?? 0) +
            ($data['other_deductions'] ?? 0);

        // Calcular net_pay
        $irpfAmount = ($totalEarnings * ($data['irpf_percentage'] ?? 0)) / 100;
        $netPay = $totalEarnings - $totalDeductions - $irpfAmount;

        // Ahora agregar estos cálculos a los datos de la nómina
        $data['days_worked'] = $daysWorked;
        $data['total_earnings'] = $totalEarnings;
        $data['total_deductions'] = $totalDeductions;
        $data['net_pay'] = $netPay;

        $payroll->update($data);

        return Inertia::location(route('payrolls.index'));
    }

    public function show(Payroll $payroll)
    {
        $employee = Employee::find($payroll->employee_id);

        return Inertia::render('Payrolls/Show', [
            'payroll' => $payroll,
            'employee' => $employee,
        ]);
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return Inertia::location(route('payrolls.index'));
    }


    public function send($payrollId)
    {
        // Obtén los datos de la nómina, el empleado y la empresa
        $payroll = Payroll::findOrFail($payrollId);
        $employee = Employee::findOrFail($payroll->employee_id);
        $company = Company::findOrFail(Auth::user()->company_id);

        // Personaliza los detalles del correo
        $fromEmail = $company->email; // Email del remitente (empresa)
        $toEmail = $employee->email;      // Email del destinatario (empleado)
        $fromName = $company->name;


        $companyEmailConfig = EmailConfiguration::where('company_id', $company->id)->first();

        // Cambiar la configuración de correo de manera dinámica
        Config::set('mail.mailers.smtp.host', $companyEmailConfig->smtp_host);
        Config::set('mail.mailers.smtp.port', $companyEmailConfig->smtp_port);
        Config::set('mail.mailers.smtp.username', $companyEmailConfig->smtp_username);
        Config::set('mail.mailers.smtp.password', $companyEmailConfig->smtp_password);
        Config::set('mail.mailers.smtp.encryption', $companyEmailConfig->smtp_encryption);
        // Generar PDF y obtener la ruta
        $pdfPath = $this->generatePayrollPdf($payrollId);

        // Enviar el correo con el PDF adjunto
        Mail::to($toEmail)->send(new PayrollEmail($payroll, $fromEmail, $fromName, $pdfPath));

    }

    public function generatePayrollPdf($payrollId)
    {
        // Obtén los datos de la nómina
        $payroll = Payroll::findOrFail($payrollId);
        $employee = Employee::findOrFail($payroll->employee_id);
        $company = Company::findOrFail(Auth::user()->company_id);

        // Genera el PDF usando una vista Blade
        $pdf = Pdf::loadView('pdfs.payroll', [
            'payroll' => $payroll,
            'employee' => $employee,
            'company' => $company,
        ]);

        // Guarda temporalmente el PDF (opcional)
        $filePath = storage_path("app/public/payroll-{$payroll->id}.pdf");
        $pdf->save($filePath);

        return $filePath;
    }


    public function print(Payroll $payroll)
    {
        $employee = Employee::find($payroll->employee_id);
        $company = Company::find(Auth::user()->company_id);

        return Inertia::render('Payrolls/Print', [
            'payroll' => $payroll,
            'employee' => $employee,
            'company' => $company,
        ]);
    }

}
