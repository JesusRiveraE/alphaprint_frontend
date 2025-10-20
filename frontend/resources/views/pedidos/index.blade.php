@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
    <h1>Listado de Pedidos</h1>
@stop

@section('content')
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Descripción</th>
                <th>Total (Lps)</th>
                <th>Estado</th>
                <th>Fecha de Creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $item)
                <tr>
                    <td>{{ $item['id_pedido'] ?? '' }}</td>
                    <td>{{ $item['cliente_nombre'] ?? '' }}</td>
                    <td>{{ $item['descripcion'] ?? '' }}</td>
                    <td>{{ number_format($item['total'] ?? 0, 2) }}</td>
                    <td>
                        <span class="badge 
                            @if($item['estado'] == 'Pendiente') bg-warning 
                            @elseif($item['estado'] == 'En Progreso') bg-info 
                            @else bg-success @endif">
                            {{ $item['estado'] ?? '' }}
                        </span>
                    </td>
                    <td>{{ $item['fecha_creacion'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
