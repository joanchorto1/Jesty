<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<h1>Factura</h1>
<p>Cliente: {{ $invoice->client->name }}</p>
<p>Fecha: {{ $invoice->date }}</p>

<p>Adjuntamos factura</p>
</body>
</html>
