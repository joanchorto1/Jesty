<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'payrolls';

    // Atributos masivos
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'payment_date',
        'days_worked',
        'days_absent',
        'base_salary',
        'overtime',
        'bonuses',
        'commissions',
        'vacation_bonus',
        'annual_bonus',
        'other_earnings',
        'income_tax',
        'social_security',
        'housing_fund',
        'loans',
        'savings_fund',
        'union_dues',
        'other_deductions',
        'irpf_percentage',
        'irpf_deduction',
        'total_earnings',
        'total_deductions',
        'net_pay'
    ];

    // Relación con el empleado
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Calcular los totales y retención de IRPF
    public function calculateTotals()
    {
        // Ingresos
        $this->total_earnings = $this->base_salary + $this->overtime + $this->bonuses + $this->commissions + $this->vacation_bonus + $this->annual_bonus + $this->other_earnings;

        // Deducciones
        $this->total_deductions = $this->income_tax + $this->social_security + $this->housing_fund + $this->loans + $this->savings_fund + $this->union_dues + $this->other_deductions;

        // Retención IRPF
        $this->irpf_deduction = ($this->irpf_percentage / 100) * $this->total_earnings;

        // Calcular el pago neto
        $this->net_pay = $this->total_earnings - $this->total_deductions - $this->irpf_deduction;
    }

    // Evento al guardar el modelo
    public static function boot()
    {
        parent::boot();

        // Calcular los totales antes de guardar el modelo
        static::saving(function ($payroll) {
            $payroll->calculateTotals();
        });
    }

    // Grupo de datos del empleado
    public function employeeData()
    {
        return [
            'employee_id' => $this->employee_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'payment_date' => $this->payment_date,
            'days_worked' => $this->days_worked,
            'days_absent' => $this->days_absent,
        ];
    }

    // Grupo de ingresos
    public function earnings()
    {
        return [
            'base_salary' => $this->base_salary,
            'overtime' => $this->overtime,
            'bonuses' => $this->bonuses,
            'commissions' => $this->commissions,
            'vacation_bonus' => $this->vacation_bonus,
            'annual_bonus' => $this->annual_bonus,
            'other_earnings' => $this->other_earnings,
        ];
    }

    // Grupo de deducciones
    public function deductions()
    {
        return [
            'income_tax' => $this->income_tax,
            'social_security' => $this->social_security,
            'housing_fund' => $this->housing_fund,
            'loans' => $this->loans,
            'savings_fund' => $this->savings_fund,
            'union_dues' => $this->union_dues,
            'other_deductions' => $this->other_deductions,
        ];
    }

    // Grupo de cálculos finales
    public function finalCalculations()
    {
        return [
            'total_earnings' => $this->total_earnings,
            'total_deductions' => $this->total_deductions,
            'irpf_deduction' => $this->irpf_deduction,
            'net_pay' => $this->net_pay,
        ];
    }
}
