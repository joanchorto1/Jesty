<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta de Producto</title>
    <style>
        @page {
            size: 60mm 30mm; /* Tamaño de etiqueta (ajústalo según tu impresora) */
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            width: 55mm;
            height: 30mm;
            margin: 0;
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-content: center;

        }
        .container {
            border: 1px dashed black; /* Guía de impresión (quitar en producción) */
            padding: 2px;
        }
        h2 {
            font-size: 12px;
            margin: 0;
            font-weight: bold;
        }
        p {
            font-size: 10px;
            margin: 2px 0;
        }
        .barcode {
            margin-top: 3px;
            text-align: center;
        }
        .barcode img {
            width: 100%;
            max-height: 20px; /* Ajustar la altura según necesidad */
        }
    </style>
</head>
<body>
<div class="container">
    <h2>{{ $product->name }}</h2>
    <p>{{ $product->description }}</p>
    <p>Precio: {{ $product->price }}€</p>
    <p>Código: {{ $product->codebar }}</p>
    <div class="barcode">
        <img src="{{ 'storage/' . $barcodePath }}" alt="Código de Barras">
    </div>
</div>
</body>
</html>
