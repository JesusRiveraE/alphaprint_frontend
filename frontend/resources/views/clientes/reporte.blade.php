<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Cliente #{{ $cliente['id_cliente'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        header { text-align: center; border-bottom: 3px solid #0c4b8e; padding: 15px 0; }
        header img { width: 100px; vertical-align: middle; }
        .company-info { display: inline-block; margin-left: 10px; vertical-align: middle; text-align: left; }
        .company-info h1 { margin: 0; color: #0c4b8e; font-size: 20px; }
        main { margin: 30px 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #999; padding: 8px; }
        th { background-color: #f2f2f2; color: #0c4b8e; }
        footer { text-align: center; font-size: 11px; color: #777; border-top: 1px solid #ddd; padding-top: 8px; margin-top: 30px; }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('img/alphaprint_logo.png') }}" alt="Logo">
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
        </table>
    </main>

    <footer>
        AlphaPrint © {{ date('Y') }} — Reporte generado automáticamente por el sistema
    </footer>
</body>
</html>
