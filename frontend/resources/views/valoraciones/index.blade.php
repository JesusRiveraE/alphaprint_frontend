@extends('adminlte::page')

@section('title', 'Valoraciones')

@section('content_header')
    <h1>Valoraciones de clientes</h1>
@stop

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Calificación</th>
                <th>Comentario</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($valoraciones as $val)
                <tr>
                    <td>{{ $val['id_valoracion'] ?? '' }}</td>
                    <td>{{ $val['cliente_nombre'] ?? 'Anónimo' }}</td>
                    <td>{{ $val['calificacion'] ?? '' }}</td>
                    <td>{{ $val['comentario'] ?? '' }}</td>
                    <td>{{ $val['fecha'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
