<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $pedido['id_pedido'] }}</title>
    <style>
        :root { --brand:#e24e60; --brand-dark:#c23c4e; }
        body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#333; }
        header { text-align:center; border-bottom:3px solid var(--brand); padding:15px 0; }
        header img { width:100px; vertical-align:middle; }
        .company-info{ display:inline-block; margin-left:10px; vertical-align:middle; text-align:left; }
        .company-info h1{ margin:0; color:var(--brand); font-size:20px; }
        main{ margin:30px 40px; }
        h2{ color:var(--brand); font-size:18px; border-bottom:2px solid var(--brand); padding-bottom:4px; }
        table{ width:100%; border-collapse:collapse; margin-top:15px; }
        th,td{ border:1px solid #999; padding:8px; }
        th{ background:#fde5e9; color:var(--brand-dark); }
        tr:nth-child(even) td{ background:#f9f9f9; }
        .tag{ background:#fde5e9; color:var(--brand-dark); padding:4px 8px; border-radius:6px; font-weight:600; font-size:11px; }
        footer{ text-align:center; font-size:11px; color:#777; border-top:1px solid #ddd; padding-top:8px; margin-top:30px; }
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
    <h2>Pedido #{{ $pedido['id_pedido'] }}</h2>
    <table>
        <tr><th>Cliente</th><td>{{ $pedido['cliente_nombre'] ?? $pedido['id_cliente'] }}</td></tr>
        <tr><th>Descripción</th><td>{{ $pedido['descripcion'] ?? '—' }}</td></tr>
        <tr><th>Total (Lps)</th><td>{{ number_format($pedido['total'] ?? 0, 2) }}</td></tr>
        <tr><th>Estado</th><td><span class="tag">{{ $pedido['estado'] ?? '—' }}</span></td></tr>
        <tr><th>Fecha de Creación</th>
            <td><span class="tag">
                {{ \Carbon\Carbon::parse($pedido['fecha_creacion'])->timezone('America/Tegucigalpa')->format('d/m/Y H:i:s') }}
            </span></td></tr>
        <tr><th>Fecha de Entrega</th><td>{{ $pedido['fecha_entrega'] ?? '—' }}</td></tr>
    </table>
</main>

<footer>
    <strong style="color:var(--brand);">AlphaPrint</strong> © {{ now('America/Tegucigalpa')->year }} — 
    Reporte generado el {{ now('America/Tegucigalpa')->format('d/m/Y H:i:s') }}
</footer>
</body>
</html>
