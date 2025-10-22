@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Listado de Clientes</h1>
        <a href="{{ route('clientes.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Nuevo Cliente
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
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente['id_cliente'] }}</td>
                            <td>{{ $cliente['nombre'] }}</td>
                            <td>{{ $cliente['telefono'] ?? '—' }}</td>
                            <td>{{ $cliente['correo'] ?? '—' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">

                                    {{-- 👁️ Ver detalles --}}
                                    <a href="{{ route('clientes.show', $cliente['id_cliente']) }}"
                                       class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- ✏️ Editar --}}
                                    <a href="{{ route('clientes.edit', $cliente['id_cliente']) }}"
                                       class="btn btn-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- 🧾 Reporte PDF --}}
                                    <a href="{{ route('clientes.reporte', $cliente['id_cliente']) }}"
                                       target="_blank" class="btn btn-secondary btn-sm" title="Imprimir reporte">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    {{-- 🗑️ Eliminar --}}
                                    <form action="{{ route('clientes.destroy', $cliente['id_cliente']) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?');">
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
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-2x mb-2"></i>
                                <p class="mb-0">No hay clientes registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
