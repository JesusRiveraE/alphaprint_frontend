@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1 class="m-0 page-title">
        <i class="fas fa-users mr-2 brand-text"></i> Clientes
    </h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <!-- Encabezado con línea roja y título -->
    <div class="card-header header-accent d-flex align-items-center justify-content-between">
        <strong class="card-title text-brand mb-0">
            <i class="fas fa-users mr-2"></i> Listado de Clientes
        </strong>
        <div class="ml-auto">
            <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-brand-outline">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Cliente
            </a>
        </div>
    </div>

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
                                <a href="{{ route('clientes.show', $item['id_cliente']) }}" class="btn btn-xs btn-outline-secondary" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $item['id_cliente']) }}" class="btn btn-xs btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('clientes.reporte', $item['id_cliente']) }}" class="btn btn-xs btn-brand-outline" title="Reporte individual">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <form action="{{ route('clientes.destroy', $item['id_cliente']) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este cliente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger" title="Eliminar">
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
/* 🎨 Paleta de color local (sin afectar el resto del proyecto) */
:root{ --brand:#e24e60; --brand-100:#fde5e9; --ink:#2b2f33; }

.page-title{ font-size:1.7rem; color:#1f2937; }
.brand-text{ color: var(--brand); }

/* 🧾 Card con línea roja */
.card-soft{ border:1px solid #f0f1f5; border-radius:.6rem; }
.card-soft.shadow-sm:hover{ box-shadow:0 0 15px rgba(226,78,96,.08); }

.header-accent{
    border-left:4px solid var(--brand);
    background:#fff;
    padding-top:.6rem;
    padding-bottom:.6rem;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.text-brand{ color: var(--brand); }

/* ➕ Botón principal a la derecha */
.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
    font-weight:600;
}
.btn-brand-outline:hover{
    background:var(--brand-100);
    color:var(--brand);
}

/* 📋 Tabla */
.thead-brand th{
    border-bottom:2px solid var(--brand) !important;
    color:#4b5563;
    font-weight:700;
}

/* ⏱️ Chip de fecha */
.badge-chip{
    background:var(--brand-100);
    color:var(--brand);
    font-weight:600;
    border-radius:999px;
    padding:.35rem .6rem;
}
</style>
@stop
