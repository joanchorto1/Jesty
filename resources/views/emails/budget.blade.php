<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuesto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<h1>Presupuesto</h1>
<p>Cliente: {{ $budget->client->name }}</p>
<p>Fecha: {{ $budget->date }}</p>

<p>Adjuntamos presupuesto</p>
</body>
</html>
