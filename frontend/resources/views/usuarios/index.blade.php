@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>Listado de Usuarios</h1>
@stop

@section('content')
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Activo</th>
                <th>Fecha de Creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $item)
                <tr>
                    <td>{{ $item['id_usuario'] ?? '' }}</td>
                    <td>{{ $item['nombre_usuario'] ?? '' }}</td>
                    <td>{{ $item['correo'] ?? '' }}</td>
                    <td>{{ $item['rol'] ?? '' }}</td>
                    <td>{{ $item['activo'] ? 'Sí' : 'No' }}</td>
                    <td>{{ $item['fecha_creacion'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
