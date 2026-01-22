<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta de Producto</title>
    <style>
        @page {
            size: 60mm 30mm;
            margin: 0;
        }
        body {
            font-family: "Helvetica Neue", Arial, sans-serif;
            text-align: center;
            width: 55mm;
            height: 30mm;
            margin: 0;
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-content: center;
            color: #111827;
            background: #ffffff;
        }
        .container {
            border: 1px solid #e5e7eb;
            padding: 4px;
        }
        h2 {
            font-size: 11px;
            margin: 0;
            font-weight: 600;
        }
        p {
            font-size: 9px;
            margin: 2px 0;
            color: #6b7280;
        }
        .barcode {
            margin-top: 4px;
            text-align: center;
        }
        .barcode img {
            width: 100%;
            max-height: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>{{ $product->name }}</h2>
    <p>{{ $product->description }}</p>
    <p>Precio: {{ $product->price }}€</p>
    <p>IVA: {{ $product->iva }}%</p>
    <p>Código: {{ $product->codebar }}</p>
    <div class="barcode">
        <img src="{{ 'storage/' . $barcodePath }}" alt="Código de Barras">
    </div>
</div>
</body>
</html>
