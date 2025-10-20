@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
    <h1>Listado de Pedidos</h1>
@stop

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $item)
                <tr>
                    <td>{{ $item['id_pedido'] ?? '' }}</td>
                    <td>{{ $item['cliente_nombre'] ?? '' }}</td>
                    <td>{{ $item['estado'] ?? '' }}</td>
                    <td>{{ $item['total'] ?? '' }}</td>
                    <td>{{ $item['fecha_creacion'] ?? '' }}</td>
                    <td>{{ $item['observaciones'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
