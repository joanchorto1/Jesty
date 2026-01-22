<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nómina #{{ $payroll->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 32px;
            background-color: #f9fafb;
            color: #111827;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            background: #fff;
            padding: 32px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        .header, .footer {
            text-align: center;
            margin: 20px 0;
        }
        .header h1 {
            margin: 0;
            color: #111827;
            font-size: 18px;
            letter-spacing: 0.02em;
        }
        .header p {
            margin: 4px 0;
            color: #6b7280;
        }
        .section-title {
            margin-top: 24px;
            padding-left: 10px;
            border-left: 2px solid #e5e7eb;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 0;
        }
        table th, table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 12px;
            text-align: left;
        }
        table th {
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            background: transparent;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        .footer p {
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Encabezado -->
    <div class="header">
        <h1>{{ $company->name }}</h1>
        <p>{{ $company->address }}</p>
        <p><strong>Nómina #{{ $payroll->id }}</strong></p>
    </div>

    <!-- Detalles del Empleado -->
    <div>
        <div class="section-title">Detalles del Empleado</div>
        <table>
            <tr>
                <th>Nombre del Empleado</th>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <th>Días Trabajados</th>
                <td>{{ $payroll->days_worked }}</td>
            </tr>
            <tr>
                <th>Días de Ausencia</th>
                <td>{{ $payroll->days_absent }}</td>
            </tr>
            <tr>
                <th>Fecha de Inicio</th>
                <td>{{ $payroll->start_date }}</td>
            </tr>
            <tr>
                <th>Fecha de Fin</th>
                <td>{{ $payroll->end_date }}</td>
            </tr>
            <tr>
                <th>Fecha de Pago</th>
                <td>{{ $payroll->payment_date }}</td>
            </tr>
        </table>
    </div>

    <!-- Ganancias -->
    <div>
        <div class="section-title">Ganancias</div>
        <table>
            <tr>
                <th>Salario Base</th>
                <td>{{ number_format($payroll->base_salary, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Bonos</th>
                <td>{{ number_format($payroll->bonuses, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Horas Extra</th>
                <td>{{ number_format($payroll->overtime, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Comisiones</th>
                <td>{{ number_format($payroll->commissions, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Bono de Vacaciones</th>
                <td>{{ number_format($payroll->vacation_bonus, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Otras Ganancias</th>
                <td>{{ number_format($payroll->other_earnings, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Ganancias Totales</th>
                <td><strong>{{ number_format($payroll->total_earnings, 2, ',', '.') }} €</strong></td>
            </tr>
        </table>
    </div>

    <!-- Deducciones -->
    <div>
        <div class="section-title">Deducciones</div>
        <table>
            <tr>
                <th>Seguridad Social</th>
                <td>{{ number_format($payroll->social_security, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Impuesto sobre la Renta</th>
                <td>{{ number_format($payroll->income_tax, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Deducción IRPF</th>
                <td>{{ number_format($payroll->irpf_deduction, 2, ',', '.') }} € ({{ $payroll->irpf_percentage }}%)</td>
            </tr>
            <tr>
                <th>Cuotas Sindicales</th>
                <td>{{ number_format($payroll->union_dues, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Préstamos</th>
                <td>{{ number_format($payroll->loans, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Fondo de Vivienda</th>
                <td>{{ number_format($payroll->housing_fund, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Fondo de Ahorro</th>
                <td>{{ number_format($payroll->savings_fund, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Otras Deducciones</th>
                <td>{{ number_format($payroll->other_deductions, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <th>Deducciones Totales</th>
                <td><strong>{{ number_format($payroll->total_deductions, 2, ',', '.') }} €</strong></td>
            </tr>
        </table>
    </div>

    <!-- Pago Neto -->
    <div>
        <div class="section-title">Pago Neto</div>
        <table>
            <tr>
                <th>Pago Neto</th>
                <td><strong>{{ number_format($payroll->net_pay, 2, ',', '.') }} €</strong></td>
            </tr>
        </table>
    </div>

    <!-- Pie de Página -->
    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y') }}</p>
    </div>
</div>
</body>
</html>
