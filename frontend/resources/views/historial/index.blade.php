@extends('adminlte::page')

@section('title', 'Historial de Estados')

@section('content_header')
    <h1 class="m-0" style="font-size:1.7rem;">
        <i class="fas fa-history mr-2 brand-text"></i> Historial de Estado de Pedidos
    </h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text" style="font-size:1.25rem;">Listado de Historial de Estados</strong>
    </div>

    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID Historial</th>
                    <th>ID Pedido</th>
                    <th>Descripción del Pedido</th>
                    <th>Estado del Pedido</th>
                    <th>Estado Anterior</th>
                    <th>Estado Nuevo</th>
                    <th>Fecha del Cambio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historial as $item)
                    @php
                        $estadoActual   = $item['estado'] ?? '';
                        $estadoAnterior = $item['estado_anterior'] ?? '';
                        $estadoNuevo    = $item['estado_nuevo'] ?? '';

                        $cls = function($e){
                            if ($e === 'Pendiente')    return 'estado-pendiente';
                            if ($e === 'En Progreso')  return 'estado-progreso';
                            if ($e === 'Completado')   return 'estado-completado';
                            if ($e === 'Cancelado')    return 'estado-cancelado';
                            return 'estado-otro';
                        };

                        // ✅ Formato de fecha con zona horaria -6 (Tegucigalpa)
                        $fechaCambio = isset($item['fecha'])
                            ? \Carbon\Carbon::parse($item['fecha'])->timezone('America/Tegucigalpa')->format('d/m/Y H:i:s')
                            : '—';
                    @endphp
                    <tr>
                        <td>{{ $item['id_historial'] ?? '' }}</td>
                        <td>{{ $item['id_pedido'] ?? '' }}</td>
                        <td>{{ $item['descripcion'] ?? '—' }}</td>

                        <td>
                            <span class="badge badge-estado {{ $cls($estadoActual) }}">
                                {{ $estadoActual }}
                            </span>
                        </td>

                        <td>
                            <span class="badge badge-estado {{ $cls($estadoAnterior) }}">
                                {{ $estadoAnterior }}
                            </span>
                        </td>

                        <td>
                            <span class="badge badge-estado {{ $cls($estadoNuevo) }}">
                                {{ $estadoNuevo }}
                            </span>
                        </td>

                        <td>{{ $fechaCambio }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<style>
:root { --brand:#e24e60; --brand-100:#fde5e9; }

/* === Marca y estructura === */
.brand-text { color: var(--brand); }
.card-soft { border:1px solid #eff1f5; border-radius:.6rem; }
.card-soft:hover { box-shadow:0 0 15px rgba(226,78,96,.08); }
.border-brand { border-left:4px solid var(--brand); background:#fff; }

/* === Badges de estado con los tonos exactos de tu imagen === */
.badge-estado{
    border-radius:.5rem;
    font-weight:700;
    padding:.35rem .6rem;
    letter-spacing:.2px;
}
.estado-pendiente   { background:#ffc107; color:#1f2937; } /* Amarillo */
.estado-progreso    { background:#17a2b8; color:#fff; }    /* Turquesa */
.estado-completado  { background:#28a745; color:#fff; }    /* Verde */
.estado-cancelado   { background:#dc3545; color:#fff; }    /* Rojo */
.estado-otro        { background:#6c757d; color:#fff; }    /* Gris */
</style>
@stop
