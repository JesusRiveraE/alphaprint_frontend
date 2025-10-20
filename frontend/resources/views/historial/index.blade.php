@extends('adminlte::page')

@section('title', 'Historial de Estados')

@section('content_header')
    <h1>Historial de Estado de Pedidos</h1>
@stop

@section('content')
<div class="card">
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
                    <tr>
                        <td>{{ $item['id_historial'] ?? '' }}</td>
                        <td>{{ $item['id_pedido'] ?? '' }}</td>
                        <td>{{ $item['descripcion'] ?? '—' }}</td>
                        <td>
                            <span class="badge 
                                @if(($item['estado'] ?? '') == 'Pendiente') bg-warning
                                @elseif(($item['estado'] ?? '') == 'Completado') bg-success
                                @elseif(($item['estado'] ?? '') == 'Cancelado') bg-danger
                                @else bg-secondary @endif">
                                {{ $item['estado'] ?? '' }}
                            </span>
                        </td>
                        <td>{{ $item['estado_anterior'] ?? '' }}</td>
                        <td>{{ $item['estado_nuevo'] ?? '' }}</td>
                        <td>{{ $item['fecha'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
