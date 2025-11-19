@extends('adminlte::page')

@section('title', 'Bitácora')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        <i class="fas fa-clipboard-list mr-2 brand-text"></i> Bitácora del Sistema
    </h1>
</div>
@stop

@section('content')
<div class="card card-soft">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text">Registros recientes</strong>
        <span class="text-muted small">
            Mostrando actividades de todos los módulos
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-brand">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th style="width: 160px;">Usuario</th>
                        <th style="width: 140px;">Módulo</th>
                        <th>Acción</th>
                        <th style="width: 170px;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacora as $item)
                        @php
                            $id        = $item['id_bitacora'] ?? $item->id_bitacora ?? '';
                            $usuario   = $item['usuario'] ?? $item->usuario ?? '—';
                            $modulo    = $item['modulo'] ?? $item->modulo ?? '—';
                            $accion    = $item['accion'] ?? $item->accion ?? '—';
                            $fechaRaw  = $item['fecha'] ?? $item->fecha ?? null;

                            // Badge por módulo (solo para dar color según módulo)
                            $moduleClass = 'badge-secondary';
                            switch (strtoupper($modulo)) {
                                case 'USUARIOS':
                                    $moduleClass = 'badge-info';
                                    break;
                                case 'PERSONAL':
                                    $moduleClass = 'badge-primary';
                                    break;
                                case 'PEDIDOS':
                                    $moduleClass = 'badge-warning';
                                    break;
                                case 'VALORACIONES':
                                    $moduleClass = 'badge-success';
                                    break;
                                case 'ARCHIVOS':
                                    $moduleClass = 'badge-dark';
                                    break;
                                default:
                                    $moduleClass = 'badge-secondary';
                                    break;
                            }

                            $fechaFormateada = $fechaRaw
                                ? \Carbon\Carbon::parse($fechaRaw, 'UTC')
                                    ->setTimezone('-06:00')
                                    ->format('d/m/Y H:i')
                                : '—';
                        @endphp

                        <tr>
                            <td class="text-muted align-middle">{{ $id }}</td>
                            <td class="align-middle">
                                <i class="fas fa-user mr-1 text-muted"></i> {{ $usuario }}
                            </td>
                            <td class="align-middle">
                                <span class="badge {{ $moduleClass }} px-3 py-1">
                                    {{ $modulo }}
                                </span>
                            </td>
                            <td class="align-middle" style="max-width: 420px; white-space: normal;">
                                {{ $accion }}
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-chip">
                                    {{ $fechaFormateada }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="far fa-folder-open mr-1"></i> No hay registros en la bitácora
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
:root{
    --brand:#e24e60;
    --brand-100:#fde5e9;
    --ink:#2b2f33;
}

/* Texto marca */
.brand-text{
    color:var(--brand);
}

/* Botón outline marca (por si luego agregas filtros/exportar) */
.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
}
.btn-brand-outline:hover{
    background:var(--brand-100);
    color:var(--brand);
}

/* Tarjeta suave */
.card-soft{
    border:1px solid #eff1f5;
    border-radius:.6rem;
}
.card-soft:hover{
    box-shadow:0 0 15px rgba(226,78,96,.08);
}

/* Encabezado con borde lateral rojo */
.border-brand{
    border-left:4px solid var(--brand);
    background:#fff;
}

/* Encabezado de tabla con línea de color */
.thead-brand th{
    border-bottom:2px solid var(--brand) !important;
    color:#4b5563;
    font-weight:700;
}

/* Chip para fecha */
.badge-chip{
    background:var(--brand-100);
    color:var(--brand);
    font-weight:600;
    border-radius:999px;
    padding:.35rem .75rem;
}
</style>
@stop