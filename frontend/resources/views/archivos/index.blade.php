@extends('adminlte::page')

@section('title', 'Archivos')

@section('content_header')
    <h1 class="m-0 page-title">
        <i class="fas fa-folder-open mr-2 brand-text"></i> Archivos
    </h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <!-- Encabezado con línea roja y botón alineado a la derecha -->
    <div class="card-header header-accent d-flex align-items-center justify-content-between">
        <strong class="card-title text-brand mb-0">
            <i class="fas fa-folder-open mr-2"></i> Listado de Archivos
        </strong>
        <div class="ml-auto">
            <a href="{{ route('archivos.create') }}" class="btn btn-sm btn-brand-outline">
                <i class="fas fa-plus mr-1"></i> Agregar Archivo
            </a>
        </div>
    </div>

    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover mb-0">
                <thead class="thead-brand">
                    <tr>
                        <th>ID</th>
                        <th>Pedido</th>
                        <th>Descripción Pedido</th>
                        <th>Estado Pedido</th>
                        <th>URL</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivos as $a)
                        <tr>
                            <td>{{ $a['id_archivo'] ?? '' }}</td>
                            <td>{{ $a['id_pedido'] ?? '' }}</td>
                            <td>{{ $a['pedido_descripcion'] ?? '-' }}</td>
                            <td>
                                @php $estado = $a['pedido_estado'] ?? '-'; @endphp
                                <span class="badge
                                    @if($estado==='Pendiente') bg-warning
                                    @elseif($estado==='En Progreso') bg-info
                                    @elseif($estado==='Completado') bg-success
                                    @else bg-secondary @endif">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($a['url']))
                                    <a href="{{ $a['url'] }}" target="_blank" class="text-brand">Abrir</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $a['comentario'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="far fa-folder-open mr-1"></i> Sin archivos registrados
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
/* 🎨 Paleta local (solo esta vista) */
:root{ --brand:#e24e60; --brand-100:#fde5e9; --ink:#2b2f33; }

/* 🔖 Título principal */
.page-title{ font-size:1.7rem; color:#1f2937; }
.brand-text{ color: var(--brand); }

/* 🧾 Card con línea roja */
.card-soft{ border:1px solid #f0f1f5; border-radius:.6rem; }
.card-soft.shadow-sm:hover{ box-shadow:0 0 15px rgba(226,78,96,.08); }

.header-accent{
    border-left:4px solid var(--brand);
    background:#fff;
    padding:.6rem 1rem;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.text-brand{ color:var(--brand); }

/* ➕ Botón agregar alineado a la derecha */
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

/* 🔗 Enlaces */
.text-brand:hover{
    text-decoration: underline;
    color: var(--brand);
}

/* 🏷️ Badges coherentes con marca */
.badge.bg-warning{ color:#8a6d1d; background:#ffefc2; }
.badge.bg-info{ color:#0b647a; background:#dff3f8; }
.badge.bg-success{ color:#1e7b39; background:#e6f6ea; }
</style>
@stop
