@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1>Listado de Empleados</h1>
@stop

@section('content')
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Área</th>
                <th>Usuario</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $item)
                <tr>
                    <td>{{ $item['id_personal'] ?? '' }}</td>
                    <td>{{ $item['nombre'] ?? '' }}</td>
                    <td>{{ $item['telefono'] ?? '' }}</td>
                    <td>{{ $item['area'] ?? '' }}</td>
                    <td>{{ $item['nombre_usuario'] ?? '' }}</td>
                    <td>{{ $item['rol'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
