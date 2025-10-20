@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Listado de Clientes</h1>
@stop

@section('content')
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Fecha de Creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $item)
                <tr>
                    <td>{{ $item['id_cliente'] ?? '' }}</td>
                    <td>{{ $item['nombre'] ?? '' }}</td>
                    <td>{{ $item['telefono'] ?? '' }}</td>
                    <td>{{ $item['correo'] ?? '' }}</td>
                    <td>{{ $item['fecha_creacion'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
