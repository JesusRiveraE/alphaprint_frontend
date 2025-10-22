<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Pedido #{{ $pedido['id_pedido'] }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            text-align: center;
            border-bottom: 3px solid #0c4b8e;
            padding: 15px 0;
        }

        header img {
            width: 100px;
            vertical-align: middle;
        }

        .company-info {
            display: inline-block;
            vertical-align: middle;
            text-align: left;
            margin-left: 10px;
        }

        .company-info h1 {
            margin: 0;
            color: #0c4b8e;
            font-size: 20px;
        }

        .company-info p {
            margin: 0;
            font-size: 11px;
            color: #555;
        }

        main {
            margin: 30px 40px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            margin-bottom: 10px;
            color: #0c4b8e;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .info-table th, .info-table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        .info-table th {
            background-color: #f2f2f2;
            color: #0c4b8e;
            text-transform: uppercase;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }

        footer {
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .signature {
            margin-top: 60px;
            text-align: center;
        }

        .signature div {
            display: inline-block;
            margin: 0 40px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 180px;
            margin: 0 auto 5px auto;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('img/alphaprint_logo.png') }}" alt="Logo AlphaPrint">
        <div class="company-info">
            <h1>AlphaPrint</h1>
            <p>Impresión Profesional y Soluciones Gráficas</p>
            <p>Av. Principal, Tegucigalpa, Honduras</p>
            <p>Tel: (504) 2200-0000 | Email: contacto@alphaprint.com</p>
        </div>
    </header>

    <main>
        <h2 class="title">Reporte de Pedido #{{ $pedido['id_pedido'] }}</h2>
        <p><strong>Fecha de emisión:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

        <table class="info-table">
            <tr>
                <th>Cliente</th>
                <td>{{ $pedido['cliente_nombre'] ?? '—' }}</td>
            </tr>
            <tr>
                <th>Descripción</th>
                <td>{{ $pedido['descripcion'] ?? '—' }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ $pedido['estado'] ?? 'Pendiente' }}</td>
            </tr>
            <tr>
                <th>Fecha de creación</th>
                <td>{{ \Carbon\Carbon::parse($pedido['fecha_creacion'])->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Fecha de entrega</th>
                <td>
                    {{ !empty($pedido['fecha_entrega'])
                        ? \Carbon\Carbon::parse($pedido['fecha_entrega'])->format('d/m/Y')
                        : '—' }}
                </td>
            </tr>
            <tr>
                <th>Total</th>
                <td><strong>L. {{ number_format($pedido['total'] ?? 0, 2) }}</strong></td>
            </tr>
        </table>

        <div class="signature">
            <div>
                <div class="signature-line"></div>
                <p>Firma del Cliente</p>
            </div>

            <div>
                <div class="signature-line"></div>
                <p>Representante AlphaPrint</p>
            </div>
        </div>
    </main>

    <footer>
        <p>AlphaPrint © {{ date('Y') }} | Reporte generado automáticamente por el sistema</p>
    </footer>
</body>
</html>
