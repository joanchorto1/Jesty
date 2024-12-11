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
            color: #333;
            background-color: #fff;
            margin: 20px;
        }

        header, footer {
            text-align: center;
            margin-bottom: 20px;
        }

        .container {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
        }

        .details, .items, .totals {
            margin-bottom: 20px;
        }

        h1, h2, h3, p {
            margin: 5px 0;
        }

        .panel {
            margin-bottom: 20px;
            background: #ffffff;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }

        .panel-heading {
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #3498db;
            color: #fff;
        }

        .totals {
            float: right;
            width: 50%;
        }

        .totals th, .totals td {
            text-align: right;
        }
    </style>
</head>
<body>
<header>
    <h1>Presupuesto #{{ $budget->id }}</h1>
    <p><strong>Fecha:</strong> {{ $budget->date }}</p>
    @if($budget->due_date)
        <p><strong>Vencimiento:</strong> {{ $budget->due_date }}</p>
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

    <div class="totals">
        <table>
            <tr>
                <th>Base Imponible:</th>
                <td>${{ number_format($budget->base_imponible, 2) }}</td>
            </tr>
            <tr>
                <th>IVA (21%):</th>
                <td>${{ number_format($budget->monto_iva, 2) }}</td>
            </tr>
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
