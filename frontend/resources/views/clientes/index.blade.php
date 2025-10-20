@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Listado de Clientes</h1>
@stop

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $item)
                <tr>
                    <td>{{ $item['id_cliente'] ?? '' }}</td>
                    <td>{{ $item['nombre'] ?? '' }}</td>
                    <td>{{ $item['correo'] ?? '' }}</td>
                    <td>{{ $item['telefono'] ?? '' }}</td>
                    <td>{{ $item['direccion'] ?? '' }}</td>
                    <td>{{ $item['fecha_registro'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
