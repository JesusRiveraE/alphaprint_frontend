@extends('adminlte::page')

@section('title', 'Notificaciones')

@section('content_header')
    <h1>Notificaciones del Sistema</h1>
@stop

@section('content')
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Pedido</th>
                <th>Mensaje</th>
                <th>Leído</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notificaciones as $item)
                <tr>
                    <td>{{ $item['id_notificacion'] ?? '' }}</td>
                    <td>{{ $item['id_pedido'] ?? '' }}</td>
                    <td>{{ $item['mensaje'] ?? '' }}</td>
                    <td>{{ $item['leido'] ? 'Sí' : 'No' }}</td>
                    <td>{{ $item['fecha'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
