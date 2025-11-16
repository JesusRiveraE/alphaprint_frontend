<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Valoraciones</title>
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

        /* ENCABEZADO TIPO AL PRIMER REPORTE */
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

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .py-1 { padding-top:4px; padding-bottom:4px; }

        .card {
            border: 1px solid #eff1f5;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .card-title {
            border-left: 4px solid var(--brand);
            padding-left: 8px;
            font-weight: bold;
            color: var(--brand);
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        thead tr {
            background-color: #fde5e9;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 6px 5px;
            vertical-align: top;
        }

        th {
            font-size: 11px;
            text-align: left;
            color: #4b5563;
        }

        td {
            font-size: 10px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #16a34a;
            color: #fff;
        }
        .badge-warning {
            background-color: #f59e0b;
            color: #fff;
        }
        .badge-danger {
            background-color: #dc2626;
            color: #fff;
        }
        .badge-secondary {
            background-color: #9ca3af;
            color: #fff;
        }

        .chip-date {
            background-color: #fde5e9;
            color: var(--brand);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 9px;
            display: inline-block;
        }

        .summary-table {
            width: 100%;
            margin-top: 4px;
        }

        .summary-table td {
            border: none;
            padding: 2px 0;
            font-size: 10px;
        }

        .summary-label {
            color: #6b7280;
        }

        .summary-value {
            font-weight: bold;
        }

        .report-meta {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 10px;
            text-align: right;
        }

        .footer {
            margin-top: 18px;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO PRINCIPAL CON LOGO (MISMO QUE EN EL OTRO REPORTE) --}}
    <header>
        <img src="{{ public_path('vendor/adminlte/dist/img/AdminLTELogo.png') }}" alt="Logo">
        <div class="company-info">
            <h1>AlphaPrint</h1>
            <p>Soluciones Gráficas Profesionales</p>
            <p>Tegucigalpa, Honduras</p>
        </div>
    </header>

    <main>
        @php
            $now = \Carbon\Carbon::now('America/Tegucigalpa');
        @endphp

        <div class="report-meta">
            <span class="chip-date">
                Generado: {{ $now->format('d/m/Y H:i') }} 
            </span><br>
            Usuario: {{ session('firebase_user.email') ?? 'Administrador' }}
        </div>

        <h2>Reporte de Valoraciones</h2>
        <p style="font-size:10px; color:#6b7280; margin-top:0; margin-bottom:10px;">
            Resumen de opiniones registradas en el sistema.
        </p>

        {{-- RESUMEN RÁPIDO (MISMA LÓGICA QUE YA TENÍAS) --}}
        @php
            $total = isset($totalValoraciones) ? $totalValoraciones : count($valoraciones ?? []);
            // promedio rápido usando colección
            $promedio = isset($promedioPuntuacion)
                ? $promedioPuntuacion
                : (collect($valoraciones ?? [])->avg(function($v){
                        $p = is_array($v) ? ($v['puntuacion'] ?? null) : ($v->puntuacion ?? null);
                        return (int)$p;
                  }) ?? 0);
            $promedio = round($promedio, 2);

            $altas = collect($valoraciones ?? [])->filter(function($v){
                $p = is_array($v) ? ($v['puntuacion'] ?? null) : ($v->puntuacion ?? null);
                return (int)$p >= 4;
            })->count();

            $bajas = collect($valoraciones ?? [])->filter(function($v){
                $p = is_array($v) ? ($v['puntuacion'] ?? null) : ($v->puntuacion ?? null);
                return (int)$p <= 2;
            })->count();
        @endphp

        <div class="card">
            <div class="card-title">Resumen general</div>
            <table class="summary-table">
                <tr>
                    <td class="summary-label">Total de valoraciones:</td>
                    <td class="summary-value">{{ $total }}</td>
                    <td class="summary-label">Promedio de puntuación:</td>
                    <td class="summary-value">{{ $promedio }} / 5</td>
                </tr>
                <tr>
                    <td class="summary-label">Valoraciones altas (4-5):</td>
                    <td class="summary-value">{{ $altas }}</td>
                    <td class="summary-label">Valoraciones bajas (1-2):</td>
                    <td class="summary-value">{{ $bajas }}</td>
                </tr>
            </table>
        </div>

        {{-- TABLA DETALLADA --}}
        <div class="card">
            <div class="card-title">Detalle de valoraciones</div>

            <table>
                <thead>
                <tr>
                    <th style="width:7%;">ID</th>
                    <th style="width:15%;">Puntuación</th>
                    <th>Comentario</th>
                    <th style="width:22%;">Fecha (GMT-6)</th>
                </tr>
                </thead>
                <tbody>
                @forelse($valoraciones as $item)
                    @php
                        $score = (int)($item['puntuacion'] ?? $item->puntuacion ?? 0);
                        $badgeClass = 'badge-secondary';
                        if ($score >= 4)      $badgeClass = 'badge-success';
                        elseif ($score == 3)  $badgeClass = 'badge-warning';
                        elseif ($score > 0)   $badgeClass = 'badge-danger';

                        $fechaRaw = $item['fecha'] ?? $item->fecha ?? null;
                        $fechaFmt = $fechaRaw
                            ? \Carbon\Carbon::parse($fechaRaw, 'UTC')
                                ->setTimezone('America/Tegucigalpa')
                                ->format('d/m/Y H:i')
                            : '—';
                    @endphp
                    <tr>
                        <td class="text-center">
                            {{ $item['id_valoracion'] ?? $item->id_valoracion ?? '' }}
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }}">
                                {{ $score }} / 5
                            </span>
                        </td>
                        <td>
                            {{ $item['comentario'] ?? $item->comentario ?? '—' }}
                        </td>
                        <td>
                            <span class="chip-date">{{ $fechaFmt }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-1" style="color:#9ca3af;">
                            No hay datos para mostrar.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer">
            Reporte generado automáticamente por el sistema Alphaprint.  
            Este documento es solo para uso interno.
        </div>
    </main>

</body>
</html>
