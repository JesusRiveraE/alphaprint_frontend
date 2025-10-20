@extends('adminlte::page')

@section('title', 'Bitácora')

@section('content_header')
    <h1>Bitácora de acciones</h1>
@stop

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Módulo</th>
                <th>Acción</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bitacora as $registro)
                <tr>
                    <td>{{ $registro['id_bitacora'] ?? '' }}</td>
                    <td>{{ $registro['modulo'] ?? '' }}</td>
                    <td>{{ $registro['accion'] ?? '' }}</td>
                    <td>{{ $registro['nombre_usuario'] ?? '---' }}</td>
                    <td>{{ $registro['rol'] ?? '---' }}</td>
                    <td>{{ $registro['fecha'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
