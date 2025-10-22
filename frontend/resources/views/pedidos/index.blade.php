@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Listado de Pedidos</h1>
        <a href="{{ route('pedidos.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Nuevo Pedido
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Descripción</th>
                        <th>Total (Lps)</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Fecha Entrega</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido['id_pedido'] }}</td>
                            <td>{{ $pedido['cliente_nombre'] ?? '—' }}</td>
                            <td>{{ $pedido['descripcion'] ?? '—' }}</td>
                            <td>L. {{ number_format($pedido['total'] ?? 0, 2) }}</td>
                            <td>
                                @php
                                    $estado = $pedido['estado'] ?? 'Pendiente';
                                    $color = match($estado) {
                                        'Completado' => 'bg-success',
                                        'En Progreso' => 'bg-warning',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $color }}">{{ $estado }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($pedido['fecha_creacion'])->format('d/m/Y H:i') }}</td>
                            <td>
                                {{ !empty($pedido['fecha_entrega'])
                                    ? \Carbon\Carbon::parse($pedido['fecha_entrega'])->format('d/m/Y')
                                    : '—' }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">

                                    {{-- 👁️ Ver detalles --}}
                                    <a href="{{ route('pedidos.show', $pedido['id_pedido']) }}"
                                       class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- ✏️ Editar --}}
                                    <a href="{{ route('pedidos.edit', $pedido['id_pedido']) }}"
                                       class="btn btn-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- 🧾 Reporte PDF --}}
                                    <a href="{{ route('pedidos.reporte', $pedido['id_pedido']) }}"
                                       target="_blank" class="btn btn-secondary btn-sm" title="Imprimir reporte">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    {{-- 🗑️ Eliminar --}}
                                    <form action="{{ route('pedidos.destroy', $pedido['id_pedido']) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Seguro que deseas eliminar este pedido?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">No hay pedidos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
