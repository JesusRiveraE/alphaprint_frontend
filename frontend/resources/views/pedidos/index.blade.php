@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
    <h1>Listado de Pedidos</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Descripción</th>
                    <th>Total (Lps)</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Fecha de Entrega</th> <!-- 🆕 Nuevo campo -->
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $item)
                    <tr>
                        <td>{{ $item['id_pedido'] ?? '' }}</td>
                        <td>{{ $item['cliente_nombre'] ?? '' }}</td>
                        <td>{{ $item['descripcion'] ?? '' }}</td>
                        <td>{{ number_format($item['total'] ?? 0, 2) }}</td>
                        <td>
                            <span class="badge 
                                @if(($item['estado'] ?? '') === 'Pendiente') bg-warning 
                                @elseif(($item['estado'] ?? '') === 'En Progreso') bg-info 
                                @elseif(($item['estado'] ?? '') === 'Completado') bg-success 
                                @else bg-secondary @endif">
                                {{ $item['estado'] ?? 'Desconocido' }}
                            </span>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($item['fecha_creacion'] ?? '')->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            @if(!empty($item['fecha_entrega']))
                                {{ \Carbon\Carbon::parse($item['fecha_entrega'])->format('d/m/Y') }}
                            @else
                                <span class="text-muted">Sin definir</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No hay pedidos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
