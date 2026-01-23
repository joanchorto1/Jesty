<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuesto #{{ $budget->id }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            background-color: #f3f4f6;
            margin: 18px;
        }

        header, footer {
            text-align: center;
            margin-bottom: 14px;
        }

        h1, h2, h3, p {
            margin: 4px 0;
        }

        h1 {
            font-size: 16px;
            font-weight: 600;
        }

        .meta {
            margin-top: 6px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            font-size: 9px;
            color: #6b7280;
        }

        .meta span {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .items, .totals {
            margin-bottom: 16px;
        }

        .panel {
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            border-radius: 10px;
            background: #fff;
        }

        .panel-heading {
            font-weight: 600;
            margin-bottom: 6px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6b7280;
            background: transparent;
        }

        .items tbody tr:last-child td {
            border-bottom: none;
        }

        .totals {
            max-width: 300px;
            margin-left: auto;
        }

        .totals table th {
            color: #6b7280;
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
    <h1>Pressupost</h1>
    <div class="meta">
        <span><strong>Data:</strong> {{ $budget->date }}</span>
        @if($budget->due_date)
            <span><strong>Venciment:</strong> {{ $budget->due_date }}</span>
        @endif
        <span><strong>Núm.:</strong> {{ $budget->id }}</span>
    </div>
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
            @foreach ($budget->items as $item)
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

        foreach ($budget->items as $item) {
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
    @endphp

    <div class="totals">
        <table>
            <tr>
                <th>Base Imponible:</th>
                <td>${{ number_format($budget->base_imponible, 2) }}</td>
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
                <th>Total:</th>
                <td>${{ number_format($budget->total, 2) }}</td>
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
