@extends('adminlte::page')

@section('title', 'Archivos')

@section('content_header')
    <h1>Gestión de Archivos</h1>
@stop

@section('content')
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>ID Pedido</th>
                <th>URL</th>
                <th>Comentario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($archivos as $item)
                <tr>
                    <td>{{ $item['id_archivo'] ?? '' }}</td>
                    <td>{{ $item['id_pedido'] ?? '' }}</td>
                    <td>
                        <a href="{{ $item['url'] }}" target="_blank" class="text-primary">
                            Ver archivo
                        </a>
                    </td>
                    <td>{{ $item['comentario'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
