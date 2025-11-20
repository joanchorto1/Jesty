<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $invoice->number ?? $invoice->name ?? $invoice->id }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            background-color: #f4f6fb;
            margin: 0;
            padding: 24px;
        }

        .container {
            max-width: 880px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.12);
        }

        header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid #eef2ff;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        header .company-name {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #1f4e79;
        }

        header p {
            margin: 2px 0;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-label {
            text-transform: uppercase;
            font-size: 9px;
            color: #6b7280;
            letter-spacing: 0.2em;
        }

        .invoice-number {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #f9fbff 100%);
        }

        .card-title {
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.2em;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .card-content p {
            margin: 2px 0;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        thead {
            background: #1f4e79;
            color: #ffffff;
        }

        th {
            font-weight: 600;
            letter-spacing: 0.05em;
            padding: 10px;
            text-transform: uppercase;
            font-size: 9px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eef2ff;
            color: #374151;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .totals {
            margin-left: auto;
            width: 50%;
        }

        .totals table {
            border: 1px solid #e5e7eb;
        }

        .totals th {
            text-align: left;
            background: #f9fafb;
            color: #6b7280;
        }

        .totals td {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }

        .total-due {
            background: #1f4e79;
            color: #ffffff;
        }

        footer {
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            margin-top: 32px;
        }

        .notes {
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            margin-top: 16px;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div>
            <div class="company-name">{{ $company->name }}</div>
            <p>{{ $company->address }}</p>
            <p>{{ $company->phone }}</p>
            <p>{{ $company->email }}</p>
            <p>{{ $company->nif }}</p>
        </div>
        <div class="invoice-meta">
            <div class="invoice-label">Factura</div>
            <div class="invoice-number">#{{ $invoice->number ?? $invoice->name ?? $invoice->id }}</div>
            <p><strong>Fecha:</strong> {{ $invoice->date }}</p>
            @if($invoice->due_date)
                <p><strong>Vencimiento:</strong> {{ $invoice->due_date }}</p>
            @endif
        </div>
    </header>

    <div class="grid">
        <div class="card">
            <div class="card-title">Empresa</div>
            <div class="card-content">
                <p><strong>{{ $company->name }}</strong></p>
                <p>{{ $company->address }}</p>
                <p>{{ $company->phone }}</p>
                <p>{{ $company->email }}</p>
                <p>ID: {{ $company->nif }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-title">Cliente</div>
            <div class="card-content">
                <p><strong>{{ $client->name }}</strong></p>
                <p>{{ $client->address }}</p>
                <p>{{ $client->phone }}</p>
                <p>{{ $client->email }}</p>
                <p>ID: {{ $client->nif }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Descuento</th>
            <th>IVA</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($item->product)->name ?? '—' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td>{{ $item->discount }}%</td>
                <td>{{ $item->iva }}%</td>
                <td>${{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <th>Base Imponible</th>
                <td>${{ number_format($invoice->base_imponible, 2) }}</td>
            </tr>
            <tr>
                <th>IVA ({{ number_format($invoice->iva, 2) }}%)</th>
                <td>${{ number_format($invoice->monto_iva, 2) }}</td>
            </tr>
            @if(($invoice->total_irpf ?? 0) > 0)
                <tr>
                    <th>Retención IRPF ({{ number_format($invoice->irpf_tax, 2) }}%)</th>
                    <td>− ${{ number_format($invoice->total_irpf, 2) }}</td>
                </tr>
            @endif
            <tr class="total-due">
                <th>Total a pagar</th>
                <td>${{ number_format($invoice->total, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
        <div class="notes">
            <strong>Notas</strong>
            <p>{{ $invoice->notes }}</p>
        </div>
    @endif

    <footer>
        <p>Gracias por confiar en nosotros</p>
        <p>© {{ date('Y') }} {{ $company->name }} — Factura generada electrónicamente</p>
    </footer>
</div>
</body>
</html>
