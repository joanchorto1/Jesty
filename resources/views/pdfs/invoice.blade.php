<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $invoice->id }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #2f3542;
            background-color: #f5f6fa;
            margin: 24px;
        }

        header, footer {
            text-align: center;
            margin-bottom: 24px;
        }

        h1, h2, h3, p {
            margin: 4px 0;
        }

        .container {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(15, 31, 53, 0.08);
        }

        .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .items, .totals {
            margin-bottom: 24px;
        }

        .panel {
            border: 1px solid #e5e9f2;
            border-left: 4px solid #2563eb;
            padding: 16px;
            border-radius: 10px;
            background: #fff;
        }

        .panel-heading {
            font-weight: 600;
            margin-bottom: 8px;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e9f2;
            text-align: left;
        }

        th {
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #2563eb;
            background: transparent;
        }

        .items tbody tr:last-child td {
            border-bottom: none;
        }

        .totals {
            max-width: 320px;
            margin-left: auto;
        }

        .totals table th {
            color: #4b5563;
            width: 55%;
        }

        .totals table td {
            text-align: right;
            color: #111827;
        }

        .totals table tr:last-child th,
        .totals table tr:last-child td {
            border-bottom: none;
        }

        footer p {
            color: #6b7280;
            font-size: 9px;
        }
    </style>
</head>
<body>
<header>
    <h1>Factura #{{ $invoice->id }}</h1>
    <p><strong>Fecha:</strong> {{ $invoice->date }}</p>
    @if($invoice->due_date)
        <p><strong>Vencimiento:</strong> {{ $invoice->due_date }}</p>
    @endif
</header>

<div class="container">
    <div class="details">
        <div class="panel">
            <div class="panel-heading">Detalles de la Empresa</div>
            <p><strong>{{ $company->name }}</strong></p>
            <p>{{ $company->address }}</p>
            <p>{{ $company->phone }}</p>
            <p>{{ $company->email }}</p>
            <p>ID: {{ $company->nif }}</p>
        </div>
        <div class="panel">
            <div class="panel-heading">Detalles del Cliente</div>
            <p><strong>{{ $client->name }}</strong></p>
            <p>{{ $client->address }}</p>
            <p>{{ $client->phone }}</p>
            <p>{{ $client->email }}</p>
            <p>ID: {{ $client->nif }}</p>
        </div>
    </div>

    <div class="items">
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Descuento</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->discount }}%</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @php
        $ivaBreakdown = [];

        foreach ($invoice->items as $item) {
            $lineBase = round($item->total ?? 0, 2);

            if ($lineBase <= 0) {
                continue;
            }

            $rate = round($item->iva ?? $item->product->iva ?? 0, 2);
            $lineTax = round(($lineBase * $rate) / 100, 2);
            $rateKey = number_format($rate, 2, '.', '');

            if (! isset($ivaBreakdown[$rateKey])) {
                $ivaBreakdown[$rateKey] = [
                    'rate' => $rate,
                    'base' => 0.0,
                    'tax' => 0.0,
                ];
            }

            $ivaBreakdown[$rateKey]['base'] += $lineBase;
            $ivaBreakdown[$rateKey]['tax'] += $lineTax;
        }

        ksort($ivaBreakdown, SORT_NUMERIC);

        $ivaBreakdown = array_map(function ($tier) {
            return [
                'rate' => $tier['rate'],
                'base' => round($tier['base'], 2),
                'tax' => round($tier['tax'], 2),
            ];
        }, $ivaBreakdown);

        $irpf = round($invoice->base_imponible * 0.15, 2);
        $total_final = round($invoice->base_imponible + $invoice->monto_iva - $irpf, 2);
    @endphp

    <div class="totals">
        <table>
            <tr>
                <th>Base Imponible:</th>
                <td>${{ number_format($invoice->base_imponible, 2) }}</td>
            </tr>
            @forelse ($ivaBreakdown as $tier)
                @php
                    $formattedRate = rtrim(rtrim(number_format($tier['rate'], 2, ',', ''), '0'), ',');
                @endphp
                <tr>
                    <th>IVA ({{ $formattedRate }}%):</th>
                    <td>
                        ${{ number_format($tier['tax'], 2) }}<br>
                        <small>Base: ${{ number_format($tier['base'], 2) }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <th>IVA:</th>
                    <td>$0.00</td>
                </tr>
            @endforelse
            <tr>
                <th>Retención IRPF (15%):</th>
                <td>− ${{ number_format($irpf, 2) }}</td>
            </tr>
            <tr>
                <th>Total a pagar:</th>
                <td><strong>${{ number_format($total_final, 2) }}</strong></td>
            </tr>
        </table>
    </div>
</div>

<footer>
    <p>Gracias por confiar en nosotros</p>
    <p>© {{ date('Y') }} {{ $company->name }}</p>
</footer>
</body>
</html>
