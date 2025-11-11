@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        <i class="fas fa-users mr-2 brand-text"></i> Listado de Clientes
    </h1>
    <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-brand-outline">
        <i class="fas fa-user-plus mr-1"></i> Nuevo Cliente
    </a>
</div>
@stop

@section('content')
<div class="card card-soft">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-brand">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Fecha Creación</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $item)
                        <tr>
                            <td class="text-muted">{{ $item['id_cliente'] ?? '' }}</td>
                            <td><strong>{{ $item['nombre'] ?? '' }}</strong></td>
                            <td>{{ $item['telefono'] ?? '' }}</td>
                            <td>{{ $item['correo'] ?? '' }}</td>
                            <td>
                                <span class="badge badge-chip">{{ $item['fecha_creacion'] ?? '' }}</span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('clientes.show', $item['id_cliente']) }}" class="btn btn-xs btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $item['id_cliente']) }}" class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('clientes.reporte', $item['id_cliente']) }}" class="btn btn-xs btn-brand-outline" title="Reporte individual">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <form action="{{ route('clientes.destroy', $item['id_cliente']) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este cliente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="far fa-folder-open mr-1"></i> No hay registros
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
:root{ --brand:#e24e60; --brand-100:#fde5e9; --ink:#2b2f33; }
.brand-text{ color: var(--brand); }
.btn-brand-outline{ border:1px solid var(--brand); color:var(--brand); background:#fff; }
.btn-brand-outline:hover{ background:var(--brand-100); color:var(--brand); }
.card-soft{ border:1px solid #f0f1f5; border-radius:.6rem; }
.thead-brand th{ border-bottom:2px solid var(--brand) !important; color:#4b5563; font-weight:700; }
.badge-chip{ background:var(--brand-100); color:var(--brand); font-weight:600; border-radius:999px; padding:.35rem .6rem; }
</style>
@stop