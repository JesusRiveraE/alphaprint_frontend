@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1>Listado de Empleados</h1>
@stop

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Área</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Fecha Ingreso</th>
                <th>Usuario</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $item)
                <tr>
                    <td>{{ $item['id_empleado'] ?? '' }}</td>
                    <td>{{ $item['nombre'] ?? '' }}</td>
                    <td>{{ $item['area'] ?? '' }}</td>
                    <td>{{ $item['correo'] ?? '' }}</td>
                    <td>{{ $item['telefono'] ?? '' }}</td>
                    <td>{{ $item['fecha_ingreso'] ?? '' }}</td>
                    <td>{{ $item['nombre_usuario'] ?? '' }}</td>
                    <td>{{ $item['rol'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
