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

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #2b2f33;
            margin: 0;
        }

        /* ENCABEZADO UNIFICADO TIPO REPORTE */
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
            font-size: 20px;
        }

        .company-info p {
            margin: 0;
            font-size: 10px;
            color: #6b7280;
        }

        main {
            margin: 20px 30px;
        }

        h2 {
            color: var(--brand);
            font-size: 16px;
            border-bottom: 2px solid var(--brand);
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        /* UTILIDADES */
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* TARJETA / CONTENEDOR */
        .card {
            border: 1px solid #eff1f5;
            border-radius: 6px;
            padding: 10px 12px;
            margin-top: 10px;
            margin-bottom: 12px;
        }

        .card-title {
            border-left: 4px solid var(--brand);
            padding-left: 8px;
            font-weight: bold;
            color: var(--brand);
            margin-bottom: 8px;
        }

        /* TABLAS */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 6px 5px;
            text-align: left;
            vertical-align: top;
            font-size: 10px;
        }

        th {
            background-color: #fde5e9;
            color: var(--brand-dark);
            width: 30%;
        }

        tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        /* CHIP / TAG FECHA */
        .tag {
            background: #fde5e9;
            color: var(--brand-dark);
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 10px;
            display: inline-block;
        }

        /* FOOTER UNIFICADO */
        .footer {
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    {{-- ENCABEZADO CON LOGO (MISMO LLAMADO QUE EN LOS OTROS REPORTES) --}}
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

        <div class="card">
            <div class="card-title">Datos del cliente</div>

            {{-- MISMA INFORMACIÓN QUE YA GENERABA, SOLO CAMBIA EL FORMATO VISUAL --}}
            <table>
                <tr>
                    <th>Nombre</th>
                    <td>{{ $cliente['nombre'] }}</td>
                </tr>
                <tr>
                    <th>Teléfono</th>
                    <td>{{ $cliente['telefono'] ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Correo</th>
                    <td>{{ $cliente['correo'] ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Fecha de Creación</th>
                    <td>
                        <span class="tag">
                            {{ \Carbon\Carbon::parse($cliente['fecha_creacion'])
                                ->timezone('America/Tegucigalpa')
                                ->format('d/m/Y H:i:s') }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </main>

    {{-- SE MANTIENE LA MISMA INFORMACIÓN DEL FOOTER, SOLO SE LE APLICA EL ESTILO GLOBAL --}}
    <div class="footer">
        <strong style="color: var(--brand);">AlphaPrint</strong> © {{ now('America/Tegucigalpa')->year }} —
        Reporte generado el {{ now('America/Tegucigalpa')->format('d/m/Y H:i:s') }}<br>
        Reporte generado automáticamente por el sistema Alphaprint.  
        Este documento es solo para uso interno.
    </div>
</body>
</html>
