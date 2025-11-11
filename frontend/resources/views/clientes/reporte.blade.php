<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Cliente #{{ $cliente['id_cliente'] }}</title>
    <style>
        :root {
            --brand: #e24e60;
            --brand-dark: #c23c4e;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        header {
            text-align: center;
            border-bottom: 3px solid var(--brand);
            padding: 15px 0;
        }
        header img {
            width: 100px;
            vertical-align: middle;
        }
        .company-info {
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
            text-align: left;
        }
        .company-info h1 {
            margin: 0;
            color: var(--brand);
            font-size: 22px;
        }
        .company-info p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }
        main {
            margin: 30px 40px;
        }
        h2 {
            color: var(--brand);
            font-size: 18px;
            border-bottom: 2px solid var(--brand);
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #fde5e9;
            color: var(--brand-dark);
        }
        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        footer {
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            margin-top: 30px;
        }
        .tag {
            background: #fde5e9;
            color: var(--brand-dark);
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('vendor/adminlte/dist/img/AdminLTELogo.png') }}" alt="Logo">
        <div class="company-info">
            <h1>AlphaPrint</h1>
            <p>Soluciones Gráficas Profesionales</p>
            <p>Tegucigalpa, Honduras</p>
        </div>
    </header>

    <main>
        <h2>Reporte de Cliente #{{ $cliente['id_cliente'] }}</h2>
        <table>
            <tr><th>Nombre</th><td>{{ $cliente['nombre'] }}</td></tr>
            <tr><th>Teléfono</th><td>{{ $cliente['telefono'] ?? '—' }}</td></tr>
            <tr><th>Correo</th><td>{{ $cliente['correo'] ?? '—' }}</td></tr>
            <tr><th>Fecha de Creación</th>
                <td>
                    <span class="tag">
                        {{ \Carbon\Carbon::parse($cliente['fecha_creacion'])
                            ->timezone('America/Tegucigalpa')
                            ->format('d/m/Y H:i:s') }}
                    </span>
                </td>
            </tr>
        </table>
    </main>

    <footer>
        <strong style="color: var(--brand);">AlphaPrint</strong> © {{ now('America/Tegucigalpa')->year }} — 
        Reporte generado el {{ now('America/Tegucigalpa')->format('d/m/Y H:i:s') }}
    </footer>
</body>
</html>