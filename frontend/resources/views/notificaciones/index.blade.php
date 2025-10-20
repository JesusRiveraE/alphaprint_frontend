@extends('adminlte::page')

@section('title', 'Notificaciones')

@section('content_header')
    <h1>Notificaciones</h1>
@stop

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pedido ID</th>
                <th>Mensaje</th>
                <th>Leído</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notificaciones as $noti)
                <tr>
                    <td>{{ $noti['id_notificacion'] ?? '' }}</td>
                    <td>{{ $noti['id_pedido'] ?? '' }}</td>
                    <td>{{ $noti['mensaje'] ?? '' }}</td>
                    <td>
                        @if(isset($noti['leido']) && $noti['leido'])
                            <span class="badge badge-success">Sí</span>
                        @else
                            <span class="badge badge-danger">No</span>
                        @endif
                    </td>
                    <td>{{ $noti['fecha'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
