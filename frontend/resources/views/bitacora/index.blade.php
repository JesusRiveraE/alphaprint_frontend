@extends('adminlte::page')

@section('title', 'Bitácora')

@section('content_header')
    <h1>Bitácora del Sistema</h1>
@stop

@section('content')
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Módulo</th>
                <th>Acción</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bitacora as $item)
                <tr>
                    <td>{{ $item['id_bitacora'] ?? '' }}</td>
                    <td>{{ $item['id_usuario'] ?? 'N/A' }}</td>
                    <td>{{ $item['modulo'] ?? '' }}</td>
                    <td>{{ $item['accion'] ?? '' }}</td>
                    <td>{{ $item['fecha'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
