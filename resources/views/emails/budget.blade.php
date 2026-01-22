<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuesto</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9fafb;
            color: #111827;
        }

        .wrapper {
            padding: 32px 16px;
        }

        .card {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 32px;
        }

        h1 {
            margin: 0 0 16px;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        p {
            margin: 8px 0;
            color: #374151;
        }

        .meta {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <h1>Presupuesto</h1>
        <p><strong>Cliente:</strong> {{ $budget->client->name }}</p>
        <p><strong>Fecha:</strong> {{ $budget->date }}</p>

        <p class="meta">Adjuntamos el presupuesto en este correo.</p>
    </div>
</div>
</body>
</html>
